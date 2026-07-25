<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\RegisterUserType;

final class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(): Response
    {
        // pour créer notre form 
        $form = $this->createForm(RegisterUserType::class);

        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}
