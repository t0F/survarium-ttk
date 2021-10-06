<?php

namespace App\Repository;

use App\Entity\GearSetTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GearSetTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GearSetTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GearSetTranslation[]    findAll()
 * @method GearSetTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GearSetTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GearSetTranslation::class);
    }

    // /**
    //  * @return GearSetTranslation[] Returns an array of GearSetTranslation objects
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
    public function findOneBySomeField($value): ?GearSetTranslation
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
