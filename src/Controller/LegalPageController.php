<?php

namespace App\Controller;

use App\Repository\LegalPageRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalPageController extends AbstractController
{
    #[Route('/legal/{slug}', name: 'app_legal_page')]
    public function show(string $slug,LegalPageRepository $legalPageRepository
    ): Response {
        $legalPage = $legalPageRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$legalPage) {
            throw $this->createNotFoundException(
                'Cette page juridique n’existe pas.'
            );
        }

        return $this->render('partials/legal_page/show.html.twig', [
            'legalPage' => $legalPage,
        ]);
    }
}
