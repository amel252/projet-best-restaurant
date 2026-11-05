<?php

namespace App\Controller\Admin;

use App\Entity\SiteInformation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SiteInformationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SiteInformation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('siteName', 'Nom')->setMaxLength(15),
            TextareaField::new('description', 'Desc')->hideOnIndex(),
            TextField::new('address', 'Adresse')->setMaxLength(20),
            TextField::new('phone', 'Tél')->setMaxLength(12),
            TextField::new('email', 'E-mail') ->setMaxLength(20),
            TextField::new('twitterUrl', 'Twitter') ->setMaxLength(15),
            TextField::new('facebookUrl', 'Faceook') ->setMaxLength(15),
            TextField::new('instagramUrl', 'Instagram') ->setMaxLength(15),
            TextField::new('linkedinUrl', 'LinkedIn') ->setMaxLength(15),
        ];
    }
}
