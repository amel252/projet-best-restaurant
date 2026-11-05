<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField; 
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MenuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),

            TextField::new('title')
                ->setLabel('Titre'),

            SlugField::new('slug')
                ->setTargetFieldName('title')
                ->setLabel('URL')
                ->hideOnIndex(),

            TextEditorField::new('description')
                ->setLabel('Description')
                ->hideOnIndex(),

            ChoiceField::new('theme', 'Thème')
                ->setChoices([
                    'Noël' => 'noel',
                    'Pâques' => 'paques',
                    'Classique' => 'classique',
                    'Évènement' => 'evenement',
    ]),

            NumberField::new('minPeople')
                ->setLabel('Nbr min de personnes'),

            NumberField::new('basePrice')
                ->setLabel('Prix de base (€)'),

            TextareaField::new('conditions')
                ->setLabel('Conditions')
                ->setRequired(false)
                ->hideOnIndex(),

            ChoiceField::new('diet', 'Régime alimentaire')
            ->setChoices([
                'Classique' => 'classique',
                'Vegan' => 'vegan',
                'Végétarien' => 'vegetarien',
            ]),

            NumberField::new('stock')
                ->setLabel('Stock dispo'),

            AssociationField::new('products')
                ->setLabel('Plats associés')
                ->setHelp('Sélectionnez les plats qui composent ce menu.')
                ->autocomplete()
                ->hideOnIndex(),
        ];
    }
    
}
