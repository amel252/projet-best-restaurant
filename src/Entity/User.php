<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/*Cela permet d'empêcher deux utilisateurs de s'inscrire avec le même email.*/ 
#[ORM\Entity]
#[UniqueEntity(
    fields: ['email'],
    message: 'Cette adresse email est déjà utilisée.'
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 20)] // Ajout ici : Le GSM exigé par le sujet
    private ?string $gsm = null;


    /*Avatar */ 
    #[ORM\Column(name:'profile_image', length: 255, nullable: true)]
    private ?string $profileImage = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class)]
    private Collection $orders;

    /**
    * @var Collection<int, Address>
    */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Address::class)]
    private Collection $addresses;

    public function __construct()
    {

    $this->roles = ['ROLE_USER'];
    /*initialiser liste adresses */ 
    $this->addresses = new ArrayCollection();
    $this->orders = new ArrayCollection();

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
        $this->roles = $roles;

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


    /*Ajout */
    public function getGsm(): ?string
    {
        return $this->gsm;
    }
    public function setGsm(string $gsm): static
    {
        $this->gsm = $gsm;
        return $this;
    }
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
    public function addAddresse(Address $address): static
    {
        //  vérif si l'addresse n'existe dans ma liste ?
        if(!$this->addresses->contains($address)){
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

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }
        return $this;
    }   

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function eraseCredentials(): void
    {
        // Si tu n'as pas de données sensibles temporaires à effacer,
        // cette méthode peut rester vide.
    }
    //  fonction pour afficher nom prenom utilisateur dans dashboard admin 
    public function __toString(): string
{
    return trim($this->firstName . ' ' . $this->lastName);

}
}

