<?php

namespace App\Controller\Admin;

use App\Entity\DeliveryNotifications;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DeliveryNotificationsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DeliveryNotifications::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
