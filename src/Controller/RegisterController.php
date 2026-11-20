<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\User;
use App\Entity\Address;
use App\Form\RegisterUserType;
use App\Classe\Mail;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



final class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Mail $mail): Response
    {
        // 1- on crée un nouveau user 
        $user = new User();

        // 2- pour créer notre form 
        $form = $this->createForm(RegisterUserType::class, $user);

        //  3- récup les données envoyés 
        $form ->handleRequest($request);

        //  4- si le form est soumis et valid 
        if($form->isSubmitted() && $form->isValid()){
            //  5- récup le mot de passe temporaire
            $plainPassword = $form->get('plainPassword')->getData();
            // 6- hasher le MDP
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plainPassword
            );
            // 7- Enregistrer le MDP
            $user->setPassword($hashedPassword);
            // 8 Création la premiere adresse de user 
            $address = new Address();

            $address->setFirstname($user->getFirstName());
            $address->setLastname($user->getLastName());
            $address->setAddress($form->get('address')->getData());
            $address->setPostalCode($form->get('postalCode')->getData());
            $address->setCity($form->get('city')->getData());
            $address->setCountry($form->get('country')->getData());
            $address->setPhoneNumber($user->getGsm());

            // Association avec le nouvel utilisateur
            $address->setUser($user);
            
            //  9- Enregister user & adresse dans BD 
            $entityManager->persist($user);
            $entityManager->persist($address);

            //  10- excuter l'insertion SQL- envoyer les donnée en BD 
            $entityManager->flush();

            // 11- envoi l'email de bienvenue
            $mailSend = $mail->send(
                (string) $user->getEmail(),
                (string) $user->getFirstName(),
                'Bienvenue chez Vite&Gourmand !',
                [
                    'firstName'=>$user->getFirstName(),
                ],
                8453422
            );
            if (!$mailSend) {
                $this->addFlash(
                    'warning',
                    'Votre compte est créé, mais le mail de bienvenue n’a pas pu être envoyé.'
                );
            }
            // 12 -redirige aprés vers page connexion
            return $this->redirectToRoute('app_home');
        }

        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}
