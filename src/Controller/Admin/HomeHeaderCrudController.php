<?php

namespace App\Controller\Admin;

use App\Entity\HomeHeader;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HomeHeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HomeHeader::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre du header'),
            TextareaField::new('content', 'Contenue du header'),
            TextField::new('btnTitle', 'titre du bouton'),
            TextField::new('btnLink', 'Lien du bouton'),
            ImageField::new('illustration')
                ->setBasePath('uploads/')
                ->setUploadDir('public/uploads')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
        ];
    }
}
