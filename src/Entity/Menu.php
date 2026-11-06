<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $theme = null;

    #[ORM\Column(nullable: true)]
    private ?array $images = null;
    /**
    * Champ temporaire pour recevoir les nouvelles photos.
    * Cette propriété n'est pas enregistrée en base de données.
    *
    * @var UploadedFile[]
    */
    private array $imagesUpload = [];

    #[ORM\Column]
    private ?int $minPeople = null;

    #[ORM\Column]
    private ?float $basePrice = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $conditions = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $diet = null;

    #[ORM\Column]
    private ?int $stock = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'menus')]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): static
    {
        $this->images = $images;

        return $this;
    }
    /**
    * @return UploadedFile[]
    */
    public function getImagesUpload(): array
    {
        return $this->imagesUpload;
    }

    /**
    * @param UploadedFile[]|null $imagesUpload
    */
    public function setImagesUpload(?array $imagesUpload): static
    {
        $this->imagesUpload = $imagesUpload ?? [];

        return $this;
    }

    public function getMinPeople(): ?int
    {
        return $this->minPeople;
    }

    public function setMinPeople(int $minPeople): static
    {
        $this->minPeople = $minPeople;

        return $this;
    }

    public function getBasePrice(): ?float
    {
        return $this->basePrice;
    }

    public function setBasePrice(float $basePrice): static
    {
        $this->basePrice = $basePrice;

        return $this;
    }

    public function getConditions(): ?string
    {
        return $this->conditions;
    }

    public function setConditions(?string $conditions): static
    {
        $this->conditions = $conditions;

        return $this;
    }

    public function getDiet(): ?string
    {
        return $this->diet;
    }

    public function setDiet(?string $diet): static
    {
        $this->diet = $diet;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->addMenu($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if($this->products->removeElement($product)){
           $product->removeMenu($this);  
        }
        

        return $this;
    }
    /**
    * Photos sélectionnées pour suppression.
    *
    * @var string[]
    */
    private array $imagesToDelete = [];
    public function getImagesToDelete(): array
    {
        return $this->imagesToDelete;
    }

    public function setImagesToDelete(?array $imagesToDelete): static
    {
        $this->imagesToDelete = $imagesToDelete ?? [];
        return $this;
    }

}
