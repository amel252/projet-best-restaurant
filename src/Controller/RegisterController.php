<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\User;
use App\Form\RegisterUserType;
// PasswordUserType
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;





final class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
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

            //  8- Enregister user dans BD 
            $entityManager->persist($user);

            //  9- excuter l'insertion SQL- envoyer les donnée en BD 
            $entityManager->flush();
            // 10 -redirige aprés vers page connexion
            return $this->redirectToRoute('app_home');
        }

        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}
