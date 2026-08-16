<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository;

final class CategoryController extends AbstractController
{
    //  si on veux récuperer un element en parametre URL on fait cette methode , pour le slug par exemple
    // 1 - préciser l'element qu'on veux chercher (slug)
    #[Route('/categorie/{slug}', name: 'app_category')]

    //  2 - on le stocke dans $category
    public function index($slug, CategoryRepository $categoryRepository): Response
    {
        // 3-  on cherche notre variable
        $category = $categoryRepository->findOneBySlug($slug);
        //  si le mot chercher n'existe pas on redirecte sur la page home

         if (!$category) {
            return $this-> redirectToRoute('app_home');
        }

        return $this->render('category/index.html.twig', [
            'category' => $category,
        ]);
    }
}
