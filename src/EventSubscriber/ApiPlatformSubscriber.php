<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Message;
use App\Repository\ChannelRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiPlatformSubscriber implements EventSubscriberInterface
{
    private $client;

    public function __construct(HttpClientInterface $client, ChannelRepository $channelRepo)
    {
        $this->client = $client;
        $this->channelRepo = $channelRepo;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                ['postMessageReceived', EventPriorities::PRE_WRITE, 200],
                ['transmitMessage', EventPriorities::PRE_WRITE, 100],
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
                $headers = ['Content-Type' => 'application/json'];
                $now = new \DateTime();
                $body = ['from' => $defaultChannel->getSenderNumber(), 'text' => $entity->getMessage(), 'data' => date_format($now, DATE_W3C)];

                $this->client->request('POST', $defaultChannel->getReceivedUrl(), ['headers' => $headers, 'body' => $body]); //->toArray();
            }
        }
    }

    public function transmitMessage(ViewEvent $event)
    {
        $entity = $event->getControllerResult();

        if ($entity instanceof Message) {
            $defaultChannel = $this->channelRepo->getDefaultChannel();
            $defaultChannel = $defaultChannel[0];

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

            if ($defaultChannel->getSentUrl()) {
                $this->postMessageSent($defaultChannel->getSentUrl());
            }
        }
    }

    public function postMessageSent($endpoint)
    {
        $headers = ['Content-Type' => 'application/json'];
        $response = $this->client->request('POST', $endpoint, ['headers' => $headers])->toArray();
    }

    public function postMessageDelivered(ViewEvent $event)
    {
        // code...
    }
}
