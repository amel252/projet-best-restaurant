<?php

namespace App\Classe;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart 
// la classe va stocker ton panier dans la session de l'utilisateur 
{
     //La session est un espace où Symfony peut conserver des infor pendant que l'utilisateur navigue sur le site.
    //request stack permet d'acceder a la session de l'utilisateur 
    public function __construct(private RequestStack $requestStack){
   
    }
    // functin qui permet d'ajouter produit en + si existe sinon redirect 
    public function add($product)
    {
        //  récup le panier stocké en session 
        $cart = $this->requestStack->getSession()->get('cart',[]);
        // on récup l'id du produit
        $productId = $product->getId();
        //  vérif si produit existe dans panier avec isset (le contenu)
        if(isset($cart[$productId])){
            //si produit existe on augmente la quantité 
                $cart[$productId]['qty']++;
        }else{
            // si produit n'existe pas on On crée donc une nouvelle ligne dans le panier 
            $cart[$productId]=[
            //  On stocke  l'objet Product, la quantité
                'object'=>$product,
                'qty'=>1
            ];

        }
        //  sauvegarde le panier (calcul de tout)
        $this->requestStack->getSession()->set('cart', $cart);
    }
    // function récup panier en cours , Elle sera utilisée par le CartController pour envoyer le panier à Twig.
    public function getCart()
    {
        return $this->requestStack
        ->getSession()
        ->get('cart',[]);
    }
}