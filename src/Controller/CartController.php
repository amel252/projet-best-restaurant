<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CartController extends AbstractController
{
    #[Route('/mon-panier/{motif}', name: 'app_cart', defaults: ['motif' => null])]
    public function index(Cart $cart, ?string $motif): Response
    {
        if ($motif === 'annulation') {
            $this->addFlash(
                'info',
                'Paiement annulé : Vous pouvez mettre à jour votre panier et votre commande'
            );
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart(),
            'totalPrice' => $cart->getTotalPrice(),
            'fullCartQuantity' => $cart->fullQuantity(),
            'deliveryPrice' => $cart->getDeliveryPrice(),
            'total' => $cart->getTotal(),
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'app_cart_add')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(int $id, Cart $cart, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneById($id);

        if (!$product) {
            return $this->redirectToRoute('app_home');
        }

        $cart->add($product);

        $this->addFlash(
            'success',
            'Le produit a bien été ajouté au panier !'
        );

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/panier/supprimer', name: 'app_cart_remove')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function remove(Cart $cart): Response
    {
        $cart->removeCart();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/panier/supprimer-produit/{id}', name: 'app_cart_remove_product')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function removeProduct(int $id, Cart $cart): Response
    {
        $cart->remove($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/panier/reduction/{id}', name: 'app_cart_decrease')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function decrease(int $id, Cart $cart): Response
    {
        $cart->decreaseCart($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/panier/augmentation/{id}', name: 'app_cart_increase')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function increase(int $id, Cart $cart): Response
    {
        $cart->increaseCart($id);

        return $this->redirectToRoute('app_cart');
    }
}

