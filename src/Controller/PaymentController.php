<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



final class PaymentController extends AbstractController
{
    #[Route('/commande/paiement', name: 'app_payment')]
    public function index(): Response
    {
        // la clé secrete pour authentifier les appels à l'api stripe
        Stripe::setApikey($_ENV['STRIPE_SECRET_KEY']);

        $YOUR_DOMAIN = 'https://127.0.0.1:8000';

        //  creation de la session stripe 
        $checkout_session = Session::create([
            //  il va me fournir une url comme 'https://checkout.stripe.com/...'
            'line_items' =>[[
                'price_data'=>[
                    'currency'=> 'eur',
                    'unit_amount'=>1500,
                    'product_data'=>[
                        'name'=> 'Produit de test'
                    ]
                ],
                'quantity'=> 1,
            ]],
            'mode'=> 'payment',
            //  stripe va te contruire sur url /success.html si paeiment ok 
            'success_url'=> $YOUR_DOMAIN .'/success.html',
            // et cancel si pas bon 
            'cancel_url'=> $YOUR_DOMAIN .'/cancel.html'
        ]);


        // ton site-> creation session stripe-> URL stripe -> Stripe checkout 
        return $this->redirect($checkout_session->url);
    }
}
