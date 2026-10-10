<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Repository\OrderRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Classe\Cart;



final class PaymentController extends AbstractController
{
    //  je peux voir quelle commande doit etre payé a l'aide de {id}
    #[Route('/commande/paiement/{id_order}', name: 'app_payment')]
    public function index($id_order , OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        // la clé secrete pour authentifier les appels à l'api stripe
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        // permet de chercher la commande correspond a l'id et appartient a l'user connecté
        $order = $orderRepository->findOneBy([
            'id'=> $id_order,
            'user'=> $this->getUser()
        ]);

        //  on vérifie que la commande existe ? sinon retour a l'acceuil
        if(!$order){
            return $this->redirectToRoute('app_home');
        }
        $products_for_stripe= [];
        //  je parcours les produits dans la commande
        foreach($order->getOrderDetails()as $product){
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    //  on récup le vrai prix , stripe attend le prix en centime par defaut
                    'unit_amount' => number_format($product->getProductPriceWt() * 100, 0, '', ''),
                    'product_data' => [
                        //  je récupere le vrai nom du produit
                        'name' => $product->getProductName(),
                        'images' => [
                        $_ENV['DOMAIN'] . '/uploads/products/' . $product->getProductIllustration()
                        ]
                    ]
                ],
                //  on récup la vraie quantity
                'quantity' => $product->getProductQuantity(),
                ];
            }
            //  on ajoute les frais de livraison
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => number_format($order->getCarrierPrice() * 100, 0, '', ''),
                    'product_data' => [
                    'name' => 'Transporteur: ' . $order->getCarrierName(),
                    ]
                ],
                'quantity' => 1,
            ];

        //  creation de la session stripe
        $checkout_session = Session::create([
            //  il va me fournir une url comme 'https://checkout.stripe.com/...'
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => $products_for_stripe ,
            'mode'=> 'payment',
            //  stripe va te contruire sur url /success.html si paeiment ok
            'success_url'=> $_ENV['DOMAIN'] .'/commande/merci/{CHECKOUT_SESSION_ID}',
            // et cancel si pas bon
            'cancel_url'=> $_ENV['DOMAIN'] .'/mon-panier/annulation'
        ]);

        $order->setStripeSessionId($checkout_session->id);
            $entityManager->flush();
        // ton site-> creation session stripe-> URL stripe -> Stripe checkout
        return $this->redirect($checkout_session->url);
    }
    //  route paiement ok
    #[Route('/commande/merci/{stripe_session_id}', name: 'app_payement_success')]
  public function success($stripe_session_id, OrderRepository $orderRepository, EntityManagerInterface $entityManager, Cart $cart): Response
  {
     $order = $orderRepository->findOneBy([
      'stripe_session_id' => $stripe_session_id,
      'user' => $this->getUser()
    ]);

    if (!$order) {
      return $this->redirectToRoute('app_home');
    }

    if ($order->getState() == 1) {
      $order->setState(2);
      $cart->remove();
      $entityManager->flush();
    }

    return $this->render('payment/success.html.twig', [
      'order' => $order,
    ]);
  }
}
