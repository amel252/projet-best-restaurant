<?php

namespace App\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\User;
use App\Form\ProfileType;
use App\Form\PasswordUserType;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Form\AddressUserType;


use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Repository\OrderRepository;



final class AccountController extends AbstractController
{

    // pour eviter de mettre entityMan dans chaque route 
    private $entityManager ;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }
    
    // fonction modif mot-de-passe compte
    #[Route('/compte/modifier-mot-de-passe', name:'app_account_edit_password')]
    public function editPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher):response
    {
        //  on cible le user connecté 
        $user=$this->getUser();
        
        // vérif si le user est connecté ? sinon redirection login 
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        //  création de formulaire
        $form = $this->createForm(PasswordUserType::class, $user);
        //  écoute la requette si le formulaire est soumis 
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
        
            //  mettre à jour le MDP 
            $newPassword = $form->get('newPassword')->getData();
            $user->setPassword(
                $passwordHasher->hashPassword($user, $newPassword)
            );
            // on le met à jour dans la BD
            $entityManager->flush();
        
            $this->addFlash(
                'success',
                'Votre mot de passe a été modifié avec succès'
            );
            //  quand tout est ok , redirige vers compte
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('account/password_edit.html.twig',[
            'modifyPwd'=> $form->createView()
        ]);
    }
    
    // Afficher la liste des commandes de l'utilisateur connecté
    #[Route('/compte/commandes', name: 'app_account_orders')]
    public function orders(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $orders = $orderRepository->findBy(
            ['user' => $user],
            ['id' => 'DESC']
        );
        $paymentStatuses = [];

        foreach ($orders as $order) {
            $sessionId = $order->getStripeSessionId();

            if ($sessionId) {
                $session = Session::retrieve($sessionId);
                $paymentStatuses[$order->getId()] = $session->payment_status;
            } else {
                $paymentStatuses[$order->getId()] = 'unpaid';
            }
        }
        return $this->render('account/orders.html.twig', [
            'orders' => $orders,
            'paymentStatuses' => $paymentStatuses,
        ]);
    }
    //  afficher la commande seule 
    #[Route('/compte/commande/{id_order}', name: 'app_account_order')]
    public function index($id_order, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findOneBy([
            'id' => $id_order,
            'user' => $this->getUser()
        ]);

        if (!$order) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('account/order/index.html.twig', [
            'order' => $order,
        ]);
    }

    

}
