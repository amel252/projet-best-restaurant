<?php

namespace App\Controller;
use App\Classe\Mail;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $mail = new Mail();

        $success = $mail->send(
            'amelghanem25@gmail.com',
            'Yves',
            'Bonjour, test de ma classe',
            '<p>Contenu du mail de test</p>'
        );

        dump($success);
        // $mail->send('amelghanem25@gmail.com', 'Yves', 'Bonjour, teste de ma class', 'contenu');
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
