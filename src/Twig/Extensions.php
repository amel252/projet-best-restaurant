<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use App\Repository\CategoryRepository;
use App\Repository\OpeningHourRepository;
use App\Repository\SiteInformationRepository;
use App\Repository\UsefullLinkRepository;
use App\Repository\LegalPageRepository;

use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use App\Classe\Cart;

class Extensions extends AbstractExtension implements GlobalsInterface
{
    private $categoryRepository;
    private $cart;
    private $openingHourRepository;
    private $siteInfoRepository;
    private $usefullLinkRepository;
    private $legalPageRepository;

    public function __construct(CategoryRepository $categoryRepository, Cart $cart , OpeningHourRepository $openingHourRepository, SiteInformationRepository $siteInfoRepository, UsefullLinkRepository $usefullLinkRepository, LegalPageRepository $legalPageRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->cart = $cart;
        $this->openingHourRepository = $openingHourRepository;
        $this->siteInfoRepository = $siteInfoRepository;
        $this->usefullLinkRepository = $usefullLinkRepository;
        $this->legalPageRepository = $legalPageRepository;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
        ];
    }

    public function formatPrice($num): string
    {
        return number_format($num, 2, ',', ' ') . ' €';
    }

    public function getGlobals(): array
    {
        return [
            'allCategories' => $this->categoryRepository->findAll(),
            'fullCartQuantity' => $this->cart->fullQuantity(),
            'openingHours' => $this->openingHourRepository->findBy([], ['id' => 'ASC']),
            'siteInformation' => $this->siteInfoRepository->findOneBy([]),
            'usefullLinks' => $this->usefullLinkRepository->findBy([],['position'=>'ASC']),
            'legalPage' => $this->usefullLinkRepository->findBy([]),
        ];
    }
}