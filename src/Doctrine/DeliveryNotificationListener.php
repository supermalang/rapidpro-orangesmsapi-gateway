<?php

namespace App\Doctrine;

use App\Entity\DeliveryNotifications;

/**
 * Entity listener.
 */
class DeliveryNotificationListener
{
    public function prePersist(DeliveryNotifications $entity)
    {
        $deliveryNotification = (object) $entity->getDeliveryInfoNotification();
        $entity->setDeliveryAddress($deliveryNotification->deliveryInfo['address']);
        $entity->setDeliveryStatus($deliveryNotification->deliveryInfo['deliveryStatus']);
        $entity->setDeliveryCallbackUuid($deliveryNotification->callbackData);
    }
}
