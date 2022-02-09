<?php

namespace App\Controller\Admin;

use App\Entity\Channel;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class ChannelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Channel::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            //IdField::new('id'),
            FormField::addTab('Channel Identification')->setIcon('info-circle'),
            TextField::new('label', 'Label'),
            TextField::new('senderName'),
            TextField::new('senderNumber'),
            BooleanField::new('isDefault')->setDisabled(),

            FormField::addTab('Authorization')->setIcon('rocket'),
            ChoiceField::new('getTokenAuthType', 'Authorization type')
                ->setChoices(['Basic Auth' => 'Basic Auth'])
                // TO DO: Add other types of auth when supported
                ->hideOnIndex(),
            TextField::new('clientId')->hideOnIndex(),
            TextField::new('clientSecret')->hideOnIndex(),
            UrlField::new('getTokenBaseUrl', 'Base URL')->setHelp('Base URL to get Authorization Token')->setDefaultColumns(5)->hideOnIndex(),

            FormField::addTab('URLs')->setIcon('link'),
            UrlField::new('sendUrl')->setDefaultColumns(5)->setHelp('The SMS server to forward messages to')->hideOnIndex(),
            UrlField::new('receivedUrl')->setDefaultColumns(5)->setHelp('The callback to inform the calling platform that message is received')->hideOnIndex(),
            UrlField::new('sentUrl')->setDefaultColumns(5)->setHelp('The callback to inform the calling platform that message is sent')->hideOnIndex(),
            UrlField::new('deliveredUrl')->setDefaultColumns(5)->setHelp('The callback to inform the calling platform that message is delivered')->hideOnIndex(),
            UrlField::new('failedUrl')->setDefaultColumns(5)->setHelp('The callback to inform the calling platform that message is not delivered')->hideOnIndex(),
            UrlField::new('stoppedUrl')->setDefaultColumns(5)->setHelp('The callback to inform the calling platform that contact asks to opt-out')->hideOnIndex(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
        ;
    }
}
