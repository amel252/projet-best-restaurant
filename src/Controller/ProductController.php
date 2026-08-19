<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;

final class ProductController extends AbstractController
{
    //  url avec le nom du produit 
    #[Route('/produit/{slug}', name: 'app_product')]
    public function index(string $slug , ProductRepository $productRepository): Response
    {
        //  on le stocke dans product variable
        $product = $productRepository->findOneBySlug($slug);
        //  si le nom n'existe pas, on retourne sur home 
        if(!$product){
            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('product/index.html.twig', [
            'product' => $product,
        ]);
    }
}
