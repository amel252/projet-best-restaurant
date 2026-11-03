<?php

namespace App\Controller\Admin;

use App\Entity\OpeningHour;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;


class OpeningHourCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OpeningHour::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
        IdField::new('id')->hideOnForm(),
        TextField::new('dayOfWeek', 'Jour'),
        TimeField::new('openTime', 'Heure d’ouverture')
            ->setRequired(false),
        TimeField::new('closeTime', 'Heure de fermeture')
            ->setRequired(false),
        BooleanField::new('isClosed', 'Fermé'),
    ];
    }
    
}
