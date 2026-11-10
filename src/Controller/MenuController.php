<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

final class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    public function index(MenuRepository $menuRepository, Request $request, PaginatorInterface $paginator): Response
    {
        /* Cette version récupère les critères envoyés dans l'URL et transmet les valeurs au repository.*/ 
        $minPrice = $request->query->get('minPrice');
        $maxPrice = $request->query->get('maxPrice');
        $theme = $request->query->get('theme');
        $diet = $request->query->get('diet');
        $minPeople = $request->query->get('minPeople');

        /* Récup les menus en BDD */ 
        $menus = $menuRepository->findFilteredMenus(
            $minPrice !== null && $minPrice !== '' ? (float) $minPrice : null,
            $maxPrice !== null && $maxPrice !== '' ? (float) $maxPrice : null,
            $theme,
            $diet,
            $minPeople !== null && $minPeople !== '' ? (int) $minPeople : null
        );
        /* pagination 9 element par page */ 
        $menus = $paginator->paginate(
            $menus,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('menu/index.html.twig', [
            'menus' => $menus,
        ]);
    }
}
