<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CartController extends AbstractController
{
    // route pour afficher l'ensemble du panier
    #[Route('/mon-panier/{motif}', name: 'app_cart', defaults:['motif'=>null])]
    public function index(Cart $cart, $motif): Response
    {


        if($motif == "annulation"){
            $this->addFlash(
                type:'info',
                message:'Paiement annulé : Vous pouvez mettre à jour votre panier et votre commande'
            );
        }
        return $this->render('cart/index.html.twig', [
            //  on récupere ce qu'on a fait dans Cart.php
            'cart' => $cart->getCart(),
            'totalPrice'=>$cart->getTotalPrice(),
            'fullCartQuantity' => $cart->fullQuantity(),
            'deliveryPrice' => $cart->getDeliveryPrice(),
            'total' => $cart->getTotal(),

        ]);
    }
    // route qui permet l'ajout produit dans panier
    #[Route('/panier/ajouter/{id}', name: 'app_cart_add')]
    //  si user n'est pas connecté on le redirige vers login
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add($id, Cart $cart, ProductRepository $productRepository):response
    {
        //  récup produit depuis BD
        $product = $productRepository->findOneById($id);

        if(!$product){
            return $this->redirectToRoute('app_home');
        }
        // ajout dans le panier
        $cart->add($product);
        // msg flash ok
        $this->addFlash(
            'success',
            'Le produit a bien été ajouté au panier !'
            );

        //  return $this-> redirect($request->headers->get('referer'));
        return $this->redirectToRoute('app_cart');
        // Aller vers la page du panier
    }
    // la route qui permet la suppression du panier
    #[Route('/panier/supprimer', name: 'app_cart_remove')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function remove(Cart $cart): Response
    {
        $cart->removeCart();

                return $this->redirectToRoute('app_home');
    }
    // route pour supprimer un produit seul
    #[Route('/panier/supprimer-produit/{id}',name:'app_cart_remove_product')]
    public function removeProduct($id, Cart $cart):Response
    {
        $cart->remove($id);
        return $this->redirectToRoute('app_cart');
    }
    //  la route pour réduction qty produits
    #[Route('/panier/reduction/{id}',name:'app_cart_decrease')]
    public function decrease($id, Cart $cart):Response
    {
        //  récup fonction dans cart.php
        $cart->decreaseCart($id);
        return $this->redirectToRoute('app_cart');
    }

    //  la route pour augmenter qty produits
    #[Route('/panier/augmentation/{id}',name:'app_cart_increase')]
    public function increase($id, Cart $cart):Response
    {
        //  récup fonction dans cart.php
        $cart->increaseCart($id);
        return $this->redirectToRoute('app_cart');
    }



}
