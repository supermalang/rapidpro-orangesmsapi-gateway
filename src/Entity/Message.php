<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 * normalizationContext={"groups"={"read"}},
 * denormalizationContext={"groups"={"write"}}
 * )
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     * @Groups({"read"})
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=30)
     * @Groups({"read", "write"})
     */
    private $sendTo;

    /**
     * @ORM\Column(type="text")
     * @Groups({"read", "write"})
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=180)
     * @Groups({"read", "write"})
     */
    private $deliveryCallbackUuid = 'N/A';

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Groups({"read", "write"})
     */
    private $messageId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read"})
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read"})
     */
    private $updated;

    /**
     * @ORM\ManyToOne(targetEntity=Channel::class, inversedBy="messages")
     */
    private $channel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"write"})
     */
    private $channelSlug;

    public function __construct()
    {
        $this->status = 'Received';
        $this->created = new \DateTime('now');
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

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getChannelSlug(): ?string
    {
        return $this->channelSlug;
    }

    public function setChannelSlug(?string $channelSlug): self
    {
        $this->channelSlug = $channelSlug;

        return $this;
    }
}
