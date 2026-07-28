<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\User;
use App\Form\ProfileType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;



final class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(Request $request ,EntityManagerInterface $entityManager): Response
    {
        $user=$this->getUser();
        // si user n'est pas connecté , redirige vers login 
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
    #[Route('/compte/modifier', name:'app_account_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager):response
    {
        // on récup notre user
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $entityManager->persist($user);
            $entityManager->flush();
            // envoyé msg notification
            $this->addFlash(
                'success',
                'vos informations ont été mises à jour'
            );

        }
        return $this->render('account/edit.html.twig',[
            'profilModifForm'=>$form,
        ]);
    }
}
