<?php

namespace App\Entity;

use App\Repository\ChannelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $getTokenAuthType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clientId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clientSecret;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $getTokenBaseUrl;

    /**
     * @ORM\OneToMany(targetEntity=Token::class, mappedBy="channel")
     */
    private $tokens;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="channel")
     */
    private $messages;

    public function __construct()
    {
        $this->tokens = new ArrayCollection();
        $this->created = new \DateTime('now');
        $this->messages = new ArrayCollection();
    }

    public function __toString(): ?string
    {
        return $this->label;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChannelSlug(): ?string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->label.'-'.$this->id)));
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

    public function getAuthorizationType(): ?string
    {
        return $this->authorizationType;
    }

    public function setAuthorizationType(string $authorizationType): self
    {
        $this->authorizationType = $authorizationType;

        return $this;
    }

    public function getGetTokenAuthType(): ?string
    {
        return $this->getTokenAuthType;
    }

    public function setGetTokenAuthType(string $getTokenAuthType): self
    {
        $this->getTokenAuthType = $getTokenAuthType;

        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(?string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(?string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    public function getGetTokenBaseUrl(): ?string
    {
        return $this->getTokenBaseUrl;
    }

    public function setGetTokenBaseUrl(string $getTokenBaseUrl): self
    {
        $this->getTokenBaseUrl = $getTokenBaseUrl;

        return $this;
    }

    /**
     * @return Collection|Token[]
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(Token $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens[] = $token;
            $token->setChannel($this);
        }

        return $this;
    }

    public function removeToken(Token $token): self
    {
        if ($this->tokens->removeElement($token)) {
            // set the owning side to null (unless already changed)
            if ($token->getChannel() === $this) {
                $token->setChannel(null);
            }
        }

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

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setChannel($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getChannel() === $this) {
                $message->setChannel(null);
            }
        }

        return $this;
    }
}
