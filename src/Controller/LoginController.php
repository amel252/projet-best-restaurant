<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class LoginController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // avoir l'erreur de connexion si y'en a 
        $error = $authenticationUtils->getLastAuthenticationError();
        //  le dernier lastName entré par le user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'=> $error,
        ]);
    }
    #[Route('/deconnexion', name: 'app_logout', methods:['GET'])]
    public function logout(): void 
    {
        throw new \logicException('This method can be blanck - it will be intercepted by logout key on you firewall');
    }
}
