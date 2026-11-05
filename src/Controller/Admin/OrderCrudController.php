<?php

namespace App\Controller\Admin;

use App\Entity\Order;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;


use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Response;

use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;


class OrderCrudController extends AbstractCrudController
{
    // Spécifie à EasyAdmin que ce CRUD gère l'entité Order
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    
     // Personnalisation du CRUD (libellés, affichage...)
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->showEntityActionsInlined();// affiche les actions à droite des lignes
    }
    
    public function configureActions(Actions $actions): Actions
    {
    $show = Action::new('Afficher', 'Afficher')
        ->linkToCrudAction('show')
        ->setIcon('fa fa-eye')
        ->addCssClass('btn btn-info');

    return $actions
        ->add(Crud::PAGE_INDEX, $show)
        ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action
                ->setLabel('Modifier')
                ->setIcon('fa fa-pencil');
        })
        ->remove(Crud::PAGE_INDEX, Action::NEW)
        ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }
    #[AdminRoute(path: '/show',name: 'show')]
    public function show(AdminContext $context): response 
    { 
        //Récupère la commande sélectionnée
        $order = $context->getEntity()->getInstance(); 
        // Vérifie que c'est bien une commande
        if (!$order instanceof Order) { 
            throw new \RuntimeException('Aucune commande trouvée.'); 
            }
        // Envoie la commande à ton template Twig , affichage personnalisé du details commande 
        return $this->render('admin/orderDetail.html.twig',[ 
            'order' => $order, 
        ]);
        
    }
    // Colonnes affichées dans la liste des commandes 
    public function configureFields(string $pageName): iterable { 
        // Formulaire de modification : // l'admin peut uniquement modifier le statut. 
        if ($pageName === Crud::PAGE_EDIT) { 
            return [ ChoiceField::new('state') 
                ->setLabel('Statut') 
                ->setChoices([ 
                    'Accepté' => 1, 
                    'En préparation' => 2, 
                    'En cours de livraison' => 3, 
                    'Livré' => 4, 
                    'En attente du retour de matériel' => 5, 
                    'Terminée' => 6, 
                    ]),  
                ]; 
            } // Liste des commandes dans EasyAdmin. 
            return [
                IdField::new('id'), 
                DateField::new('createdAt') ->setLabel('Date'), 
                ChoiceField::new('state') ->setLabel('Statut') 
                    ->setChoices([ 
                        'Accepté' => 1, 
                        'En préparation' => 2, 
                        'En cours de livraison' => 3, 
                        'Livré' => 4, 
                        'En attente du retour de matériel' => 5, 
                        'Terminée' => 6, 
                    ]) 
                    ->renderAsBadges(),
                AssociationField::new('user')
                    ->setLabel('Utilisateur'), 
                TextField::new('carrierName')
                    ->setLabel('Transporteur'), 
                NumberField::new('totalTva') ->setLabel('Total TVA') ->setNumDecimals(2),
                NumberField::new('totalTtc') ->setLabel('Total TTC') ->setNumDecimals(2), 
            ];
    }
    
}
