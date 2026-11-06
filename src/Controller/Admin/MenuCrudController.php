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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MenuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }
    /* fonction */ 
    private ParameterBagInterface $params;
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }
    /* fonction créer dossier uploads/menus & donne nom unique & ajoute nv noms au table images*/ 
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Menu) {
            $this->uploadImages($entityInstance);
        }
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Menu) {
            $this->deleteImages($entityInstance);
            $this->uploadImages($entityInstance);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
    /*fonction de telechargement photos */ 
    private function uploadImages(Menu $menu): void
    {
        $files = $menu->getImagesUpload();
        // Aucune nouvelle photo : on conserve les anciennes.
        if (empty($files)) {
            return;
        }
        $uploadDir = $this->params->get('kernel.project_dir') . '/public/uploads/menus';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        // On récupère les photos déjà enregistrées.
        $images = $menu->getImages() ?? [];
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }
            // Extension déterminée à partir du fichier.
            $extension = $file->guessExtension();
            if (!$extension) {
                continue;
            }
            // Nom unique pour éviter les doublons.
            $fileName = bin2hex(random_bytes(16)) . '.' . $extension;
            $file->move($uploadDir, $fileName);
            $images[] = $fileName;
        }
        $menu->setImages($images);
        $menu->setImagesUpload([]);
    }  
    /*fonction de suppression imgs dans dossier uploads */ 
    private function deleteImages(Menu $menu): void
    {
        $imagesToDelete = $menu->getImagesToDelete();
        if (empty($imagesToDelete)) {
            return;
        }
        $uploadDir = $this->params->get('kernel.project_dir') . '/public/uploads/menus';
        $images = $menu->getImages() ?? [];

        foreach ($imagesToDelete as $image) {
            // Vérifie que le nom correspond bien à un fichier simple.
            if (basename($image) !== $image) {
                continue;
            }
            // Ne supprimer que les images réellement associées au menu
            if (!in_array($image, $images, true)) {
                continue;
            }
            $filePath = $uploadDir . '/' . $image;
            if (is_file($filePath)) {
                unlink($filePath);
            }

            $images = array_values(array_diff($images, [$image]));
        }

        $menu->setImages($images);
        $menu->setImagesToDelete([]);
    }
    
    public function configureFields(string $pageName): iterable
    {
        $imageChoices = [];
        $context = $this->getContext();
        if ($context !== null && $context->getEntity() !== null) {
            $menu = $context->getEntity()->getInstance();
            if ($menu instanceof Menu) {
            foreach ($menu->getImages() ?? [] as $image) {
                $imageChoices[$image] = $image;
            }
        }
    }
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
            /* Ce champ permettra de sélectionner plusieurs images en même temps*/ 
            Field::new('imagesUpload', 'Photos du menu')
            ->setFormType(FileType::class)
            ->setFormTypeOptions([
                'multiple' => true,
                'mapped' => true,
                'required' => false,
                'attr' => [
                    'accept' => 'image/jpeg,image/png,image/webp',
                ],
                'constraints' => [
                    new All([
                        new File(
                            maxSize:'5M',
                            mimeTypes :['image/jpeg','image/png','image/webp'],
                            mimeTypesMessage :'Choisissez des images JPG, PNG ou WebP.',
                        ),
                    ]),
                ],
            ])
            ->onlyOnForms(),
            Field::new('imagesToDelete', 'Photos à supprimer')
                ->setFormType(ChoiceType::class)
                ->setFormTypeOptions([
                    'choices' => $imageChoices,
                    'multiple' => true,
                    'expanded' => true,
                    'mapped' => true,
                    'required' => false,
                ])
                ->onlyOnForms(),
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
