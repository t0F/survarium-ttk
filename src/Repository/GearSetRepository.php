<?php

namespace App\Repository;

use App\Entity\GearSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GearSet|null find($id, $lockMode = null, $lockVersion = null)
 * @method GearSet|null findOneBy(array $criteria, array $orderBy = null)
 * @method GearSet[]    findAll()
 * @method GearSet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GearSetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GearSet::class);
    }

    // /**
    //  * @return GearSet[] Returns an array of GearSet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GearSet
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
