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
    #[Route('/commande/resume-panier', name:'app_order_summary', methods: ['POST'])]
    public function add(Request $request , Cart $cart, CarrierRepository $carrierRepository):Response
    {
       
        // récup des adresses 
        $addresses = $this->getUser()->getAddresses();
        // récup le livreur 
          $carriers = $carrierRepository->findAll();
        //  on crée le form  et on met les données envoyé par le form  
        $form = $this->createForm(OrderType::class, null,[
            'addresses' => $addresses,
            'carriers' => $carriers,
        ]);

        // récup les données envoyé par form 
        $form->handleRequest($request);
        
        //  si form est envoyé et valide , redirige vers order
        if($form->isSubmitted() && $form->isValid()){
            //  return $this->redirectToRoute('app_order');
            $choices = $form->getData();
         return $this->render('order/summary.html.twig',[
            'choices'=> $choices,
            'cart'=>$cart->getCart(),
            'totalWt'=>$cart->getTotalPrice()
            ]);
        }
        // Si le formulaire est invalide,
        // on retourne sur la page de commande avec les erreurs
        return $this->render('order/index.html.twig', [
            'deliverForm' => $form->createView(),
        ]);
    }
}