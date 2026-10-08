<?php

namespace App\Twig;

// Classe de base permettant de créer une extension Twig
use Twig\Extension\AbstractExtension;
// Permet de créer un filtre personnalisé Twig
use Twig\TwigFilter;


// Notre classe est une extension Twig , Elle implémente GlobalsInterface pour pouvoir créer des variables globales
class Extensions extends AbstractExtension 
{

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
}