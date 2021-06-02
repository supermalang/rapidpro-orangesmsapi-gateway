<?php

namespace App\Entity;

use App\Repository\ChannelRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChannelRepository::class)
 */
class Channel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDefault;

    /**
     * @ORM\Column(type="text")
     */
    private $authorization;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sendUrl;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $SenderNumber;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $senderName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $receivedUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sentUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deliveredUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $failedUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stoppedUrl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    public function getAuthorization(): ?string
    {
        return $this->authorization;
    }

    public function setAuthorization(string $authorization): self
    {
        $this->authorization = $authorization;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getSendUrl(): ?string
    {
        return $this->sendUrl;
    }

    public function setSendUrl(string $sendUrl): self
    {
        $this->sendUrl = $sendUrl;

        return $this;
    }

    public function getSenderNumber(): ?string
    {
        return $this->SenderNumber;
    }

    public function setSenderNumber(string $SenderNumber): self
    {
        $this->SenderNumber = $SenderNumber;

        return $this;
    }

    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    public function setSenderName(?string $senderName): self
    {
        $this->senderName = $senderName;

        return $this;
    }

    public function getReceivedUrl(): ?string
    {
        return $this->receivedUrl;
    }

    public function setReceivedUrl(?string $receivedUrl): self
    {
        $this->receivedUrl = $receivedUrl;

        return $this;
    }

    public function getSentUrl(): ?string
    {
        return $this->sentUrl;
    }

    public function setSentUrl(?string $sentUrl): self
    {
        $this->sentUrl = $sentUrl;

        return $this;
    }

    public function getDeliveredUrl(): ?string
    {
        return $this->deliveredUrl;
    }

    public function setDeliveredUrl(?string $deliveredUrl): self
    {
        $this->deliveredUrl = $deliveredUrl;

        return $this;
    }

    public function getFailedUrl(): ?string
    {
        return $this->failedUrl;
    }

    public function setFailedUrl(?string $failedUrl): self
    {
        $this->failedUrl = $failedUrl;

        return $this;
    }

    public function getStoppedUrl(): ?string
    {
        return $this->stoppedUrl;
    }

    public function setStoppedUrl(?string $stoppedUrl): self
    {
        $this->stoppedUrl = $stoppedUrl;

        return $this;
    }
}
