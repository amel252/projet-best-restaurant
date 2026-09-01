<?php

namespace App\Controller\Account\Profile;

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
use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Form\AddressUserType;

final class ProfileController extends AbstractController
{

   
    //  route profile 
    #[Route('/profile', name: 'app_profile')]
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
                    //  CHEMIN DE STOCKAQE
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
        return $this->render('account/profile/index.html.twig',[
            'user'=> $user,
        ]);
    }
    // fonction modif infos profil 
    #[Route('/profile/modifier-infos', name:'app_profile_edit')]
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
                return $this->redirectToRoute('app_profile');
        }
            // Notification d'erreur
        $this->addFlash(
            'danger',
            'Le formulaire contient des erreurs.'
        );

        }
        return $this->render('account/profile/edit_profil.html.twig',[
            'profilModifForm'=>$form->createView(),
        ]);
    }
    
}
