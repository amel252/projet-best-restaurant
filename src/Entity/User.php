<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
// interface de sécurity 
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/*Cela permet d'empêcher deux utilisateurs de s'inscrire avec le même email.*/ 
#[ORM\Entity]
#[UniqueEntity(
    fields: ['email'],
    message: 'Cette adresse email est déjà utilisée.'
)]

// #[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    //  on met email a unique 
    #[ORM\Column(length: 255, unique:true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    /*Avatar */ 
    #[ORM\Column(name:'profile_image', length: 255, nullable: true)]
    private ?string $profileImage = null;


    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Address::class)]
    /*mapped fait refernce a user */ 
    /*déclarer addresses :  sorte de liste d'objets address */ 
    private Collection $addresses;

    public function __construct()
    {
    $this->roles = ['ROLE_USER'];
    /*initialiser liste adresses */ 
    $this->addresses = new ArrayCollection();
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
    
    public function getRoles(): array
    {
        return $this->roles;
        
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles ;
       
        return $this;

    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }
    // getter et seter avatar
    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): static
    {
        $this->profileImage = $profileImage;

    return $this;
    }

    // récupérer la liste des adresses de l'utilisateur.
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    //  add address
    public function addAddresse(Address $adress): static
    {
        //  vérif si l'addresse n'existe dans ma liste ?
        if(!$this->address->contains($address)){
            // ajoute l'addresse dans la collection
            $this->addresses->add($address);
            //  faire le lien avec user -> cette addresse appartient a ce user 
            $address->setUser($this);
        }
        return $this;

    }

    //  remove addresse 
    public function removeAddress(Address $address): static
    {
        //  si l'addresse est trouvé on la ca retire de la collection
        if($this->addresses->removeElement($address)){
            // Est-ce que l'utilisateur de cette adresse est bien l'utilisateur actuel ?
            if($address->getUser()=== $this){
                //  si oui je supprime aussi le lien vers cet utilisateur
                $address->setUser(null);
            }
        }
        // je retourne l'utilisateur.
        return $this;
    }

}
