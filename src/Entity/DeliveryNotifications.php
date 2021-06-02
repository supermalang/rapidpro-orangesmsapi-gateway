<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\DeliveryNotificationsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\EntityListeners({"App\Doctrine\DeliveryNotificationListener"})
 * @ORM\Entity(repositoryClass=DeliveryNotificationsRepository::class)
 */
class DeliveryNotifications
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $DeliveryCallbackUuid;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $deliveryAddress;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $deliveryStatus;

    /**
     * @ORM\Column(type="json")
     */
    private $deliveryInfoNotification = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeliveryCallbackUuid(): ?string
    {
        return $this->DeliveryCallbackUuid;
    }

    public function setDeliveryCallbackUuid(string $DeliveryCallbackUuid): self
    {
        $this->DeliveryCallbackUuid = $DeliveryCallbackUuid;

        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): self
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function getDeliveryStatus(): ?string
    {
        return $this->deliveryStatus;
    }

    public function setDeliveryStatus(string $deliveryStatus): self
    {
        $this->deliveryStatus = $deliveryStatus;

        return $this;
    }

    public function getDeliveryInfoNotification(): ?array
    {
        return $this->deliveryInfoNotification;
    }

    public function setDeliveryInfoNotification(array $deliveryInfoNotification): self
    {
        $this->deliveryInfoNotification = $deliveryInfoNotification;

        return $this;
    }
}
