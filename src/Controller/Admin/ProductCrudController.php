<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProductCrudController extends AbstractCrudController
{
    //  rajout
    public function __construct(
        private ParameterBagInterface $params
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }
     public function configureFields(string $pageName): iterable
    {
        $required = true;
        if ($pageName === 'edit') {
            $required = false;
        }
        return [
            TextField::new('name')->setLabel('Nom')->setHelp('Nom du produit'),
            SlugField::new('slug')->setTargetFieldName('name')->setLabel('URL')->setHelp('URL de la categorie génerée'),
            TextEditorField::new('description')->setLabel('Description')->setHelp('La description du produit'),

            ImageField::new('illustration')
            ->setLabel('Image')->setHelp('Image du produit en 600x600px')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            
            // ->setUploadedFileNamePattern('[year]-[month]-[day]-[contentHash].[extension]')

            ->setBasePath('/uploads/products')
            ->setUploadDir($this->params->get('kernel.project_dir') . '/public/uploads/products'),

            NumberField::new('price')->setLabel('Prix H.T')->setHelp('Le prix H.T du produit'),
            ChoiceField::new('tva')
                ->setLabel('Taux de TVA')
                ->setChoices([
                '5%' => 5,
                '10%' => 10,
                '20%' => 20
            ]),
            AssociationField::new('category', 'Categorie associée')



        ];
    }

}
    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */

