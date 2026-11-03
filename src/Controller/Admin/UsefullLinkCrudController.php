<?php

namespace App\Controller\Admin;

use App\Entity\UsefullLink;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;



class UsefullLinkCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UsefullLink::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre')
                ->setMaxLength(100),

            TextField::new('url', 'Lien')
                ->setMaxLength(255),

            IntegerField::new('position', 'Ordre d’affichage'),
        ];
    }
   

      
}
