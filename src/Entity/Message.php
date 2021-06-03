<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $sendTo;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $deliveryCallbackUuid;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $messageId;

    public function __construct()
    {
        $this->status = 'Received';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSendTo(): ?string
    {
        return $this->sendTo;
    }

    public function setSendTo(string $sendTo): self
    {
        $this->sendTo = $sendTo;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getDeliveryCallbackUuid(): ?string
    {
        return $this->deliveryCallbackUuid;
    }

    public function setDeliveryCallbackUuid(string $deliveryCallbackUuid): self
    {
        $this->deliveryCallbackUuid = $deliveryCallbackUuid;

        return $this;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function setMessageId(?string $messageId): self
    {
        $this->messageId = $messageId;

        return $this;
    }
}
