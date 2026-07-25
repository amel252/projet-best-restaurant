<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\RegisterUserType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        //  on crée un nouveau user 
        $user = new User();

        // pour créer notre form 
        $form = $this->createForm(RegisterUserType::class, $user);
        //  écoute la requette si le form est soumis 
        $form ->handleRequest($request);
        //  si le form est soumis et valid 
        if($form->isSubmitted() && $form->isValid()){
            //  dire à Doctrine de sauver le form dans BD 
            $entityManager->persist($user);
            //  envoyer les donnée en BD 
            $entityManager->flush();
            return $this->redirectToRoute('app_register');
        }

        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}
