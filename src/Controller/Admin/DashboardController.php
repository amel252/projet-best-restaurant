<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Carrier;
use App\Entity\Allergen;
use App\Entity\OpeningHour;
use App\Entity\SiteInformation;

use App\Controller\Admin\UserCrudController;
use App\Controller\Admin\CategoryCrudController;
use App\Controller\Admin\OpeningHurCrudController;
use App\Controller\Admin\SiteInformationCrudController;

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
        yield MenuItem::linkToRoute('Categories', 'fas fa-list', 'admin_category_index');
        yield MenuItem::linkToRoute('Produits', 'fas fa-tag', 'admin_product_index');
        yield MenuItem::linkToRoute('Transporteurs', 'fas fa-truck', 'admin_carrier_index');
        yield MenuItem::linkToRoute('Commandes', 'fas fa-sort', 'admin_order_index');
        yield MenuItem::linkToRoute('Allergènes', 'fas fa-leaf', 'admin_allergen_index');
    
        // Gestion du contenu du site
        yield MenuItem::subMenu('Footer', 'fas fa-shoe-prints')
            ->setSubItems([
                MenuItem::linkToRoute("Horaires d'ouvertures", 'fas fa-clock', 'admin_opening_hour_index'),
                MenuItem::linkToRoute("Information du site", 'fas fa-info-circle', 'admin_site_information_index'),
                MenuItem::linkToRoute('Liens utiles','fas fa-link','NOM_DE_TA_ROUTE'
                ),
            ]);
    }
}
