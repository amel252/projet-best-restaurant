<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }
    public function findFilteredMenus(
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?string $theme = null,
        ?string $diet = null,
        ?int $minPeople = null
    ):array {
        $qb = $this->createQueryBuilder('m');
        if($minPrice !== null){
            $qb->andWhere('m.basePrice >= :minPrice')
                ->setParameter('minPrice', $minPrice);
        }
        if ($maxPrice !== null) {
        $qb->andWhere('m.basePrice <= :maxPrice')
           ->setParameter('maxPrice', $maxPrice);
        }
        if ($theme !== null && $theme !== '') {
        $qb->andWhere('m.theme = :theme')
           ->setParameter('theme', $theme);
        }
        if ($diet !== null && $diet !== '') {
        $qb->andWhere('m.diet = :diet')
           ->setParameter('diet', $diet);
        }
        if ($minPeople !== null) {
        $qb->andWhere('m.minPeople <= :minPeople')
           ->setParameter('minPeople', $minPeople);
        }
        return $qb->getQuery()->getResult();
    }
    //    /**
    //     * @return Menu[] Returns an array of Menu objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Menu
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
