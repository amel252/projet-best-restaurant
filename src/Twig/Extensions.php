<?php

namespace App\Twig;

// Classe de base permettant de créer une extension Twig
use Twig\Extension\AbstractExtension;
use App\Repository\CategoryRepository;
use Twig\Extension\GlobalsInterface;
// Permet de créer un filtre personnalisé Twig
use Twig\TwigFilter;
use App\Classe\Cart;


// Notre classe est une extension Twig , Elle implémente GlobalsInterface pour pouvoir créer des variables globales
class Extensions extends AbstractExtension implements GlobalsInterface
{
    // récup 2 variables
    private $categoryRepository;
    private $cart;

    public function __construct(CategoryRepository $categoryRepository,Cart $cart) 
    {
        $this->categoryRepository = $categoryRepository;
        $this->cart = $cart;
    }

    // Cette méthode permet de créer des filtres Twig personnalisés
    public function getFilters(): array
    {
        return
        [
             // On crée un filtre appelé "price" , {{ product.price|price }} , Quand Twig utilise |price, il appellera la méthode formatPrice() au lieu de faire  |number_format(2, ',', ' ') }} €
            new TwigFilter('price', [$this, 'formatPrice']),
            // price sert simplement à afficher les prix correctement.
        ];
    }

    // Fonction pour formater le prix avec 2 décimales et le symbole €
    public function formatPrice($num): string
    {
        return number_format($num, 2, ',', ' ') . ' €';
    }

     //  function permettant d'afficher toutes les categories
    public function getGlobals(): array
    {
        // Creation de la variable global qu'on peut utiliser partout
        return
        [
         'allCategories' => $this->categoryRepository->findAll(),
         'fullCartQuantity' => $this->cart->fullQuantity(),
        ];
    }
}