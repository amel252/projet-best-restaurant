<?php

namespace App\Controller\Admin;

use App\Entity\Order;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;


use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

// use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;


class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->showEntityActionsInlined();
    }
      public function configureActions(Actions $actions): Actions
    {
        // suppression des actions ds easyAdmin
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            // ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable 
    { 
        if ($pageName === Crud::PAGE_EDIT) 
            { yield ChoiceField::new('state') 
                ->setLabel('Statut') 
                ->setChoices([ 
                    'Accepté' => 1, 
                    'En préparation' => 2, 
                    'En cours de livraison' => 3, 
                    'Livré' => 4, 
                    'En attente du retour de matériel' => 5, 
                    'Terminée' => 6, 
                ]);
            return; 
        }

        yield IdField::new('id') ->setLabel('N°'); 
        yield DateField::new('createdAt') ->setLabel('Date') ->hideOnForm(); 
        yield ChoiceField::new('state') ->setLabel('Statut') ->setChoices(
            [ 'Accepté' => 1, 
            'En préparation' => 2, 
            'En cours de livraison' => 3, 
            'Livré' => 4, 
            'En attente du retour de matériel' => 5, 
            'Terminée' => 6, 
            ]) 
            ->renderAsBadges(); 
        yield AssociationField::new('user') ->setLabel('Utilisateur') ->hideOnForm(); 
        yield TextField::new('carrierName') ->setLabel('Transporteur') ->hideOnForm(); 
        yield NumberField::new('totalTva') ->setLabel('Total TVA') ->setNumDecimals(2) ->hideOnForm();
        yield NumberField::new('totalTtc') ->setLabel('Total TTC') ->setNumDecimals(2) ->hideOnForm();
    }
    
}
