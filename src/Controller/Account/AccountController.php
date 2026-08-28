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
use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Form\AddressUserType;

final class AccountController extends AbstractController
{

    // pour eviter de mettre entityMan dans chaque route 
    private $entityManager ;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }
    
    // fonction modif mot-de-passe compte
    #[Route('/compte/modifier-mot-de-passe', name:'app_account_edit_password')]
    public function editPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher):response
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
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('account/password_edit.html.twig',[
            'modifyPwd'=> $form->createView()
        ]);
    }
}
