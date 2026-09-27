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
use App\Entity\Order;
use App\Entity\OrderDetail;



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
    #[Route('/commande/recapitulatif', name:'app_order_summary', methods: ['POST'])]
    public function add(Request $request , Cart $cart, CarrierRepository $carrierRepository , EntityManagerInterface $entityManager):Response
    {
       
        // récup des adresses 
        $addresses = $this->getUser()->getAddresses();
        // récup le livreur 
          $carriers = $carrierRepository->findAll();
          //  récup les produits 
        $products = $cart->getCart();
        //  on crée le form  et on met les données envoyé par le form  
        $form = $this->createForm(OrderType::class, null,[
            'addresses' => $addresses,
            'carriers' => $carriers,
        ]);

        // récup les données envoyé par form 
        $form->handleRequest($request);
        
        //  si form est envoyé et valide , redirige vers order
        if($form->isSubmitted() && $form->isValid()){

             $choices = $form->getData();

            //  stcocker les infos  dans la BD ,  Création de la chaine d'addresse
            $addressObj = $form->get('addresses')->getData();

            $address = $addressObj->getFirstname() . ' ' . $addressObj->getLastname() . '<br/>';
            $address .= $addressObj->getAddress() . '<br/>';
            $address .= $addressObj->getPostalCode() . ' ' . $addressObj->getCity() . '<br/>';
            $address .= $addressObj->getCountry() . '<br/>';
            $address .= $addressObj->getPhoneNumber();

            $order = new Order();
            
             $order->setCreatedAt(new \DateTime());
             $order->setState(1);
             $order->setCarrierName($form->get('carriers')->getData()->getName());
             $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
             $order->setDelivery($address); 

                // Création d'un nouvel objet orderDetail
                foreach ($products as $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductIllustration($product['object']->getIllustration());
                $orderDetail->setProductPrice($product['object']->getPrice());
                $orderDetail->setProductTva($product['object']->getTva());
                $orderDetail->setProductQuantity($product['qty']);
                $order->addOrderDetail($orderDetail);

                // Important
                // $entityManager->persist($orderDetail);
            }

            $entityManager->persist($order);
            $entityManager->flush();
        } 
         return $this->render('order/summary.html.twig',[
            'choices'=> $choices,
            'cart'=>$products,
            'totalWt'=>$cart->getTotalPrice()
        ]);
    }
}