<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(Request $request ,EntityManagerInterface $entityManager): Response
    {
        $user=$this->getUser();
        // si user n'est pas connecté
        if(!$user){
            return $this->redirectToRoute('app_login');
        }
        
        $profileImage = $request->files->get('profileImage');
        if($profileImage){
            $newFilename= uniqid() .'.' . $profileImage->guessExtension();
            try{
                $profileImage->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/profile',
                    $newFilename
                );
                //  mettre à jour img 
                $user->setProfileImage($newFilename);
                // envoyer les donnée en BD 
                $entityManager->persist($user);
                $entityManager->flush();

            } catch(FileException $e){
                $this->addFlash(
                    'error',
                    'Une erreur est survenue lors du téléchargement de l\image.'
                );

            }

        }
        return $this->render('account/index.html.twig',[
            'user'=> $user,
        ]);
    }
}
