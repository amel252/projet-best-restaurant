<?php

namespace App\Controller\Account\Address;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// use App\Entity\User;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Form\AddressUserType;

final class AddressController extends AbstractController
{

    // pour eviter de mettre entityMan dans chaque route 
    private $entityManager ;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    //  Création de la route Affichage du template (adresse)
    #[Route('/compte/addresses', name: 'app_account_addresses')]
    public function addresses(): Response
    {
        return $this->render('account/address/index.html.twig');
    }

    /*Route création addresse */  
    #[Route('/compte/addresses/ajout/{id}',name:'app_account_address_form', defaults:['id'=>null])]
    public function addressForm(Request $request, ?int $id, AddressRepository $addressRepository):response
    {
         // Vérification utilisateur connecté
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // si l'id est fourni , on récupere l'adresse 
        if($id){
            // addresse en BD on récup
            $address = $addressRepository->findOneById($id);
            
                //  on compare addresse en BD et addres donné par user
                if(!$address or $address->getUser() != $user){
                    // si ne sont pas identique , redirige vers la page pour remmetre la bonne addresse 
                    return $this->redirectToRoute('app_account_addresses');
                }
            }else{
                //  j'instancié l'addresse 
                $address = new Address();
                //  je stocke
                $address->setUser($user);
            }
            //  création de formulaire 
            $form = $this->createForm(AddressUserType::class, $address);
            //  écoute la requette si form est soumis
            $form->handleRequest($request);
            // si le form est soumis est valid 
            if($form->isSubmitted()&& $form->isValid()){
                $this->entityManager->persist($address);
                $this->entityManager->flush();

                $this->addFlash(
                    type:'success',
                    message:'Votre addresse a été ajoutée avec success! '
                );
                return $this->redirectToRoute('app_account_addresses');
            }
            return $this->render('account/address/form.html.twig', [
                'addressForm' => $form
        ]);

    }
    //  route de suppression l'adresse 
   #[Route('/compte/adresses/suppression/{id}', name: 'app_account_address_delete', requirements:['id' => '\d+'])]
    public function deleteForm(request $request, int $id, AddressRepository $addressRepository): Response
    {
         // Vérifier que l'utilisateur est connecté
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        // récup l'addresse
        $address = $addressRepository->findOneById($id);
        //  vérif l'addresse si appartient a ce user 
        if (!$address || $address->getUser() !== $user) {
            //  si ne sont pas identique , redirige vers la page pour qu'il remet l'adresse correct
            return $this->redirectToRoute('app_account_addresses');
        }
          // Supprimer
        $this->entityManager->remove($address);
        $this->entityManager->flush();

        // message
        $this->addFlash(
            type:'success',
            message:'Votre adresse a été supprimé avec success !'
        );
        
        return $this->redirectToRoute('app_account_addresses');


    }
}
