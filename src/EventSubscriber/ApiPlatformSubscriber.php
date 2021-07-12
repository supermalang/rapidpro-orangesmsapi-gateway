<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\DeliveryNotifications;
use App\Entity\Message;
use App\Repository\ChannelRepository;
use App\Repository\MessageRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiPlatformSubscriber implements EventSubscriberInterface
{
    private $client;

    public function __construct(HttpClientInterface $client, ChannelRepository $channelRepo, MessageRepository $messageRepo)
    {
        $this->client = $client;
        $this->channelRepo = $channelRepo;
        $this->messageRepo = $messageRepo;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                // To Handle later. Need to be called only when our service receives an incoming message to forward to RapidPro
                // ['postMessageReceived', EventPriorities::PRE_WRITE, 200],
                ['transmitMessage', EventPriorities::PRE_WRITE, 100],
                ['postMessageDelivered', EventPriorities::PRE_WRITE, 50],
            ],
        ];
    }

    public function postMessageReceived(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
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
    public function transmitMessage(ViewEvent $event)
    {
        $entity = $event->getControllerResult();

        if ($entity instanceof Message) {
            $defaultChannel = $this->channelRepo->getDefaultChannel();
            $defaultChannel = $defaultChannel[0];

            $duplicatedMessage = $this->messageRepo->findOneWithMessageId($entity->getMessageId());
            // To make sure the message is not sent several times
            if (is_null($duplicatedMessage)) {
                $authToken = $defaultChannel->getAuthorization();
                $address = $defaultChannel->getSendUrl();

                $endpoint = str_replace('{{SENDER_NUMBER}}', $defaultChannel->getSenderNumber(), $address);

                $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer '.$authToken];

                $body = ['outboundSMSMessageRequest' => [
                    'address' => 'tel:+'.str_replace('+', '', $entity->getSendTo()),
                    'senderAddress' => 'tel:+'.str_replace('+', '', $defaultChannel->getSenderNumber()),
                    'senderName' => $defaultChannel->getSenderName(),
                    'outboundSMSTextMessage' => ['message' => $entity->getMessage()],
                ]];

                $response = $this->client->request('POST', $endpoint, ['headers' => $headers, 'json' => $body])->toArray();
                $results = $response['outboundSMSMessageRequest'];

                $explodedUrl = explode('/', $results['resourceURL']);

                $entity->setDeliveryCallbackUuid(end($explodedUrl));

                $messageId = null != $entity->getMessageId() ? $entity->getMessageId() : null;

                if ($defaultChannel->getSentUrl()) {
                    $this->postMessageSent($defaultChannel->getSentUrl(), $messageId);
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
}
