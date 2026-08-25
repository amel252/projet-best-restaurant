<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CartController extends AbstractController
{
    // route pour afficher l'ensemble du panier
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            //  on récupere ce qu'on a fait dans Cart.php
            'cart' => $cart->getCart()
            
        ]);
    }
    // route qui permet l'ajout produit dans panier
    #[Route('/panier-ajout/{id}', name: 'app_cart_add')]
    //  si user n'est pas connecté on le redirige vers login
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add($id, Cart $cart, ProductRepository $productRepository, Request $request):response
    {
        //  récup produit depuis BD 
        $product = $productRepository->findOneById($id);

        if(!$product){
            return $this->readirectToRoute('app_home');
        }
        // ajout dans le panier
        $cart->add($product);
        
        return $this->redirectToRoute('app_cart');
        // Aller vers la page du panier
    }
    // la route qui permet la suppression du panier
    #[Route('/panier/supprimer', name: 'app_cart_remove')]
    public function remove(Cart $cart): Response
    {
        $cart->removeCart();
    
                return $this->redirectToRoute('app_home');
    }
    //  la route pour réduction qty produits 
    #[Route('/panier/reduction/{id}',name:'app_cart_decrease')]
    public function descrease($id, Cart $cart):response
    {
        //  récup fonction dans cart.php
        $cart->decreaseCart($id);
        return $this->redirectToRoute('app_cart');
    }
}
