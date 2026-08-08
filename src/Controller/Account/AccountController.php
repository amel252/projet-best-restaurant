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
                    //  CHEMIN DE STOCK
                    $this->getParameter('kernel.project_dir') . '/public/uploads/profile',
                    $newFilename
                );
                //  mettre à jour img 
                $user->setProfileImage($newFilename);
                // envoyer les donnée en BD 
                $entityManager->persist($user);
                $entityManager->flush();
                    $this->addFlash(
                        'success',
                        "l'image est mise a jour."
                    );

            } catch(FileException $e){
                $this->addFlash(
                    'danger',
                    'Une erreur est survenue lors du téléchargement de l\image.'
                );
            }
        }
        return $this->render('account/index.html.twig',[
            'user'=> $user,
        ]);
    }
    // fonction modif infos profil 
    #[Route('/compte/modifier-infos', name:'app_account_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager):response
    {
        // on récup notre user
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                $entityManager->flush();
                // envoyé msg notification
                $this->addFlash(
                type:'success',
                message:'vos informations ont été mises à jour'
            );
                return $this->redirectToRoute('app_account');
        }
            // Notification d'erreur
        $this->addFlash(
            'danger',
            'Le formulaire contient des erreurs.'
        );

        }
        return $this->render('account/edit.html.twig',[
            'profilModifForm'=>$form->createView(),
        ]);
    }
    // fonction modif mot-de-passe compte
    #[Route('/compte/modifier-mot-de-passe', name:'app_account_edit_password')]
    public function edit_password(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher):response
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
            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/password.html.twig',[
            'modifyPwd'=> $form->createView()
        ]);
    }
}
