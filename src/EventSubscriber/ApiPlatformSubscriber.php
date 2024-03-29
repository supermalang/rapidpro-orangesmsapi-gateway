<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\DeliveryNotifications;
use App\Entity\Message;
use App\Entity\Token;
use App\Repository\ChannelRepository;
use App\Repository\MessageRepository;
use App\Repository\TokenRepository;
use App\Service\OrangeTokenHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiPlatformSubscriber implements EventSubscriberInterface
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
            KernelEvents::VIEW => [
                // To Handle later. Need to be called only when our service receives an incoming message to forward to RapidPro
                // ['postMessageReceived', EventPriorities::PRE_WRITE, 200],
                ['transmitMessage', EventPriorities::PRE_WRITE, 100],
                ['postMessageDelivered', EventPriorities::PRE_WRITE, 50],
                ['setCreatedTime', EventPriorities::PRE_WRITE, 25],
                ['setModifiedTime', EventPriorities::PRE_WRITE, 20],
            ],
        ];
    }

    public function postMessageReceived(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        if ($entity instanceof Message) {
            $channelToUse = $this->channelRepo->getDefaultChannel();
            $channelToUse = $channelToUse[0];

            if ($channelToUse->getReceivedUrl()) {
                $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
                $now = new \DateTime();
                $body = ['from' => $entity->getSendTo(), 'text' => $entity->getMessage(), 'date' => date_format($now, DATE_W3C)];

                $this->client->request('POST', $channelToUse->getReceivedUrl(), ['headers' => $headers, 'body' => $body]); //->toArray();
            }
        }
    }

    /** Transmit the message to the SMS endpoint Platform */
    public function transmitMessage(ViewEvent $event)
    {
        $entity = $event->getControllerResult();

        if ($entity instanceof Message) {
            $now_time = new \DateTime('now');
            $authToken = null;

            $chosenChannelSlug = null == $entity->getChannelSlug() ? null : explode('-', $entity->getChannelSlug());
            $chosenChannelId = null == $chosenChannelSlug ? null : end($chosenChannelSlug);
            $channelToUse = null == $chosenChannelId ? $this->channelRepo->getDefaultChannel() : $this->channelRepo->find($chosenChannelId);

            if (null == $channelToUse) {
                $response = new Response();
                $response->setContent(json_encode(['message' => 'Unable to identify channel to use. Please specify a good channel']));
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(Response::HTTP_BAD_GATEWAY);
                $event->setResponse($response);

                return;
            }
            $entity->setChannel($channelToUse);

            /**
             * We need to check whether we have a valid auth token for this channel. If not, we request for a new token before sending SMS. That process will happen in several steps:
             * 1. Get last token for current channel
             * 2. Check token expiry date
             * 3. If expired:
             *      a. get new token
             *      b. add the token to the DB and use it for our request.
             */
            $lastToken = $this->tokenRepo->findOneWithChannelId($channelToUse->getId());
            $lastTokenExpiryDate = null == $lastToken ? null : $lastToken->getExpireDate();

            // If token has expired or does not exist in DB
            if ($now_time > $lastTokenExpiryDate || null == $lastTokenExpiryDate) {
                // Get a new token
                $authToken = $this->oth->getNewToken($channelToUse->getClientId(), $channelToUse->getClientSecret(), $channelToUse->getGetTokenBaseUrl());

                // If token is good we save it in the DB
                if (null != $authToken) {
                    $tokenEntity = new Token();
                    $tokenEntity->setType($authToken['token_type']);
                    $tokenEntity->setAccessToken($authToken['access_token']);
                    $tokenExpireDate = $now_time->add(new \DateInterval('PT'.$authToken['expires_in'].'S')); // adds `expires_in` seconds (3600 by default)
                    $tokenEntity->setCreateDate(new \DateTime('now'));
                    $tokenEntity->setExpireDate($tokenExpireDate);
                    $tokenEntity->setChannel($channelToUse);

                    $this->em->persist($tokenEntity);
                    $this->em->flush();

                    $lastToken = $tokenEntity;
                } else {
                    $response = new Response();
                    $response->setContent(json_encode(['message' => 'Unable to get authorization from the sending server. Please try again later.']));
                    $response->headers->set('Content-Type', 'application/json');
                    $response->setStatusCode(Response::HTTP_BAD_GATEWAY);

                    $event->setResponse($response);

                    return;
                }
            }

            // To make sure the message is not sent several times
            $duplicatedMessage = $this->messageRepo->findOneWithMessageId($entity->getMessageId());
            if (is_null($duplicatedMessage)) {
                $address = $channelToUse->getSendUrl();

                $endpoint = str_replace('{{SENDER_NUMBER}}', $channelToUse->getSenderNumber(), $address);

                $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer '.$lastToken->getAccessToken()];

                $body = ['outboundSMSMessageRequest' => [
                    'address' => 'tel:+'.str_replace('+', '', $entity->getSendTo()),
                    'senderAddress' => 'tel:+'.str_replace('+', '', $channelToUse->getSenderNumber()),
                    'senderName' => $channelToUse->getSenderName(),
                    'outboundSMSTextMessage' => ['message' => $entity->getMessage()],
                ]];

                // Request to send new SMS
                $response = $this->client->request('POST', $endpoint, ['headers' => $headers, 'json' => $body])->toArray();
                $results = $response['outboundSMSMessageRequest'];

                $explodedUrl = explode('/', $results['resourceURL']);

                $entity->setDeliveryCallbackUuid(end($explodedUrl));

                $messageId = null != $entity->getMessageId() ? $entity->getMessageId() : null;

                if ($channelToUse->getSentUrl()) {
                    $this->postMessageSent($channelToUse->getSentUrl(), $messageId);
                }
            } else {
                $response = new Response();
                $response->setContent(json_encode(['message' => 'A message with this messageId already exists in the database']));
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(Response::HTTP_TOO_EARLY);

                $event->setResponse($response);
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
    public function postMessageDelivered(ViewEvent $event)
    {
        $entity = $event->getControllerResult();

        if ($entity instanceof DeliveryNotifications && 'DeliveredToTerminal' == $entity->getDeliveryStatus()) {
            $channelToUse = $this->channelRepo->getDefaultChannel();
            $channelToUse = $channelToUse[0];

            $endpoint = $channelToUse->getDeliveredUrl();

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

    public function setCreatedTime(ViewEvent $event)
    {
        $entity = $event->getControllerResult();

        if (method_exists($entity, 'setCreated')) {
            $entity->setCreated(new \DateTimeImmutable());
        }
    }

    public function setModifiedTime(ViewEvent $event)
    {
        $entity = $event->getControllerResult();

        if (method_exists($entity, 'setModified')) {
            $entity->setModified(new \DateTimeImmutable());
        }
        if (method_exists($entity, 'setUpdated')) {
            $entity->setUpdated(new \DateTimeImmutable());
        }
    }
}
