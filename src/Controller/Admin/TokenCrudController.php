<?php

namespace App\Controller\Admin;

use App\Entity\Token;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TokenCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Token::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Authorization tokens')
            ->setHelp('index', 'Tokens are automatically generated')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('channel')->setDefaultColumns(5),
            TextField::new('type')->setDefaultColumns(5),
            TextField::new('access_token')->setDefaultColumns(5)->hideOnIndex(),
            DateTimeField::new('createDate')->hideOnForm()->setDefaultColumns(5),
            DateTimeField::new('expireDate')->hideOnForm()->setDefaultColumns(5),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
        ;
    }
}
