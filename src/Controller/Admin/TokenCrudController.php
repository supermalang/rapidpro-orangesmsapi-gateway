<?php

namespace App\Controller\Admin;

use App\Entity\Token;
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
}
