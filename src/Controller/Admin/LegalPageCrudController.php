<?php

namespace App\Controller\Admin;

use App\Entity\LegalPage;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LegalPageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LegalPage::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            TextField::new('slug', 'Slug'),
            TextEditorField::new('content','Contenu'),
            DateTimeField::new('updatedAt', 'Dernière modification')->hideOnForm(),
            
        ];
    }
    
}
