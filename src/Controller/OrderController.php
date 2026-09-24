<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use App\Form\OrderType;
use App\Repository\CarrierRepository;
use App\Classe\Cart;



final class OrderController extends AbstractController
{
    #[Route('/commande/livraison', name: 'app_order')]
    public function index(CarrierRepository $carrierRepository):Response
    {
        // récul le user connecté puis récup son addresse 
        $addresses = $this->getUser()->getAddresses();
        // Si aucune adresse, redirection vers la création d'adresse
        if(count($addresses) ===0){
            return $this->redirectToRoute('app_account_address_form');
        }
        //  récup le livreur 
        $carriers = $carrierRepository->findAll();
        // je crée un form puis transmet l'adresse 
        $form = $this->createForm(OrderType::class, null,[
            "addresses" =>$addresses,
            "carriers"=>$carriers,
            // quand user valide le form, les données vont être envoyées /commande/resume-panier
            'action' =>$this->generateUrl('app_order_summary'),
        ]);
          return $this->render('order/index.html.twig', [
            'deliverForm' => $form->createView(),
        ]);
    }

    //  route pour voir le panier pour que ca soit envoyer en BD 
    #[Route('/commande/resume-panier', name:'app_order_summary')]
    public function add(Request $request , Cart $cart, EntityManagerInterface $entityManager , CarrierRepository $carrierRepository):Response
    {
        //  vérif si requet est en post, si qlq vient directement sur le lien je le redirige vers app_cart
        if($request->getMethod() !== 'POST'){
            return $this->redirectToRoute('app_cart');
        }

        // récup le livreur 
          $carriers = $carrierRepository->findAll();
        //  on crée le form  et on met les données envoyé par le form  
        $form = $this->createForm(OrderType::class, null,[
            'addresses' => $this->getUser()->getAddresses(),
            'carriers' => $carriers,
        ]);

        // prends les données dans $request et mets-les dans mon formulaire
        $form->handleRequest($request);
        //  si form est envoyé et valide , redirige vers order
        if($form->isSubmitted() && $form->isValid()){
            //  return $this->redirectToRoute('app_order');
        }
         return $this->render('order/summary.html.twig',[
            'choices'=> $form->getData(),
            'cart'=>$cart->getCart(),
            'totalWt'=>$cart->getTotalPrice()
        ]);
    }
}
