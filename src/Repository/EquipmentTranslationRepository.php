<?php

namespace App\Repository;

use App\Entity\EquipmentTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EquipmentTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipmentTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipmentTranslation[]    findAll()
 * @method EquipmentTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipmentTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipmentTranslation::class);
    }

    // /**
    //  * @return EquipmentTranslation[] Returns an array of EquipmentTranslation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EquipmentTranslation
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
