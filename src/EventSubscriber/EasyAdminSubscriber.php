<?php

namespace App\EventSubscriber;

use App\Entity\DeliveryNotifications;
use App\Entity\Message;
use App\Entity\Token;
use App\Repository\ChannelRepository;
use App\Repository\MessageRepository;
use App\Repository\TokenRepository;
use App\Service\OrangeTokenHelper;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $client;

    public function __construct(HttpClientInterface $client, ChannelRepository $channelRepo, MessageRepository $messageRepo, TokenRepository $tokenRepo, OrangeTokenHelper $oth, EntityManagerInterface $em)
    {
        $this->client = $client;
        $this->channelRepo = $channelRepo;
        $this->messageRepo = $messageRepo;
        $this->tokenRepo = $tokenRepo;
        $this->oth = $oth;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => [
                ['transmitMessage', 110],
                ['postMessageDelivered', 60],
                ['setCreatedTime', 25],
            ],
            BeforeEntityUpdatedEvent::class => [
                ['setModifiedTime', 20],
            ],
        ];
    }

    public function postMessageReceived(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        if ($entity instanceof Message) {
            $defaultChannel = $this->channelRepo->getDefaultChannel();
            $defaultChannel = $defaultChannel[0];

            if ($defaultChannel->getReceivedUrl()) {
                $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
                $now = new \DateTime();
                $body = ['from' => $entity->getSendTo(), 'text' => $entity->getMessage(), 'date' => date_format($now, DATE_W3C)];

                $this->client->request('POST', $defaultChannel->getReceivedUrl(), ['headers' => $headers, 'body' => $body]); //->toArray();
            }
        }
    }

    /** Transmit the message to the SMS endpoint Platform */
    public function transmitMessage(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Message) {
            $now_time = new \DateTime('now');
            $authToken = null;

            $defaultChannel = $this->channelRepo->getDefaultChannel();
            $defaultChannel = $defaultChannel[0];

            /**
             * We need to check whether we have a valid auth token for this channel. If not, we request for a new token before sending SMS. That process will happen in several steps:
             * 1. Get last token for current channel
             * 2. Check token expiry date
             * 3. If expired:
             *      a. get new token
             *      b. add the token to the DB and use it for our request.
             */
            $lastToken = $this->tokenRepo->findOneWithChannelId($defaultChannel->getId());
            $lastTokenExpiryDate = null == $lastToken ? null : $lastToken->getExpireDate();

            // If token has expired or does not exist in DB
            if ($now_time > $lastTokenExpiryDate || null == $lastTokenExpiryDate) {
                // Get a new token
                $authToken = $this->oth->getNewToken($defaultChannel->getClientId(), $defaultChannel->getClientSecret(), $defaultChannel->getGetTokenBaseUrl());

                // If token is good we save it in the DB
                if (null != $authToken) {
                    $tokenEntity = new Token();
                    $tokenEntity->setType($authToken['token_type']);
                    $tokenEntity->setAccessToken($authToken['access_token']);
                    $tokenExpireDate = $now_time->add(new \DateInterval('PT'.$authToken['expires_in'].'S')); // adds `expires_in` seconds (3600 by default)
                    $tokenEntity->setCreateDate(new \DateTime('now'));
                    $tokenEntity->setExpireDate($tokenExpireDate);
                    $tokenEntity->setChannel($defaultChannel);

                    $this->em->persist($tokenEntity);
                    $this->em->flush();

                    $lastToken = $tokenEntity;
                } else {
                    // TODO: Fire Exception
                    return;
                }
            }

            // To make sure the message is not sent several times
            $duplicatedMessage = $this->messageRepo->findOneWithMessageId($entity->getMessageId());
            if (is_null($duplicatedMessage)) {
                $address = $defaultChannel->getSendUrl();

                $endpoint = str_replace('{{SENDER_NUMBER}}', $defaultChannel->getSenderNumber(), $address);

                $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer '.$lastToken->getAccessToken()];

                $body = ['outboundSMSMessageRequest' => [
                    'address' => 'tel:+'.str_replace('+', '', $entity->getSendTo()),
                    'senderAddress' => 'tel:+'.str_replace('+', '', $defaultChannel->getSenderNumber()),
                    'senderName' => $defaultChannel->getSenderName(),
                    'outboundSMSTextMessage' => ['message' => $entity->getMessage()],
                ]];

                // Request to send new SMS
                $response = $this->client->request('POST', $endpoint, ['headers' => $headers, 'json' => $body])->toArray();
                $results = $response['outboundSMSMessageRequest'];

                $explodedUrl = explode('/', $results['resourceURL']);

                $entity->setDeliveryCallbackUuid(end($explodedUrl));

                $messageId = null != $entity->getMessageId() ? $entity->getMessageId() : null;

                if ($defaultChannel->getSentUrl()) {
                    $this->postMessageSent($defaultChannel->getSentUrl(), $messageId);
                }
            } else {
                // TODO: Fire Exception

                return;
            }
        }
    }

    /** Notify the Calling Platform that the message has been sent */
    public function postMessageSent($endpoint, $messageId = null)
    {
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];

        if (isset($messageId)) {
            return $this->client->request('POST', $endpoint, ['headers' => $headers, 'body' => ['id' => $messageId]]);
        }

        return $this->client->request('POST', $endpoint, ['headers' => $headers]);
    }

    /** Notify the Calling Platform that the message has been delivered */
    public function postMessageDelivered(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof DeliveryNotifications && 'DeliveredToTerminal' == $entity->getDeliveryStatus()) {
            $defaultChannel = $this->channelRepo->getDefaultChannel();
            $defaultChannel = $defaultChannel[0];

            $endpoint = $defaultChannel->getDeliveredUrl();

            $message = $this->messageRepo->findOneWithDeliveryCallbackUuid($entity->getDeliveryCallbackUuid());

            if (!is_null($message)) {
                $messageId = $message->getMessageId();
                $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];

                if (isset($messageId)) {
                    return $this->client->request('POST', $endpoint, ['headers' => $headers, 'body' => ['id' => $messageId]]);
                }
            }
        }
    }

    public function setCreatedTime(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (method_exists($entity, 'setCreated')) {
            $entity->setCreated(new \DateTimeImmutable());
        }
    }

    public function setModifiedTime(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (method_exists($entity, 'setModified')) {
            $entity->setModified(new \DateTimeImmutable());
        }
        if (method_exists($entity, 'setUpdated')) {
            $entity->setUpdated(new \DateTimeImmutable());
        }
    }
}
