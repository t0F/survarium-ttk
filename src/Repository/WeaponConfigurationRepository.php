<?php

namespace App\Repository;

use App\Entity\WeaponConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method WeaponConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method WeaponConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method WeaponConfiguration[]    findAll()
 * @method WeaponConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeaponConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WeaponConfiguration::class);
    }

    // /**
    //  * @return WeaponConfiguration[] Returns an array of WeaponConfiguration objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WeaponConfiguration
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
