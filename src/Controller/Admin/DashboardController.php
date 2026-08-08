<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\User;
use App\Entity\Categorie;
use App\Controller\Admin\UserCrudController;
use App\Controller\Admin\CategorieCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
// Cette classe permet de fabriquer des URLs vers une page EasyAdmin.

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    // ce fichier est le point d'entrée de mon interface d'admin 
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');

        // quand tu arrives sur: http://localhost:8000/admin - Au lieu d'afficher la page Dashboard par défaut, on va rediriger directement vers une page CRUD.

        // Tu demandes au conteneur Symfony : Donne-moi un objet AdminUrlGenerator.
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        // //  redirection par defaut vers User Crud
        // return $this->redirect(
        //     $adminUrlGenerator
        //     // Tu lui dis :Je veux l'URL correspondant au CRUD des utilisateurs
        //     ->setController(UserCrudController::class)
        //     ->generateUrl()
        // );
       
    }

    public function configureDashboard(): Dashboard
    {
          // Config générale du tableau de bord
        return Dashboard::new()
        // Le texte affiché en haut à gauche de l'administration
            ->setTitle('Best Restaurant');
    }

    public function configureMenuItems(): iterable
    {
         // Les éléments qui s'affcihent dans le menu vertical à gauche
    
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        
        yield MenuItem::linkToRoute('Utilisateurs', 'fas fa-user','admin_user_index');
            
        yield MenuItem::linkToRoute('Categories', 'fas fa-list', 'admin_categorie_index');
    }
}
