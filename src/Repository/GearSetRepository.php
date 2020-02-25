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

    /**
      * @return GearSet[] Returns an array of GearSet objects, including equipments of sets
      */
    public function getGearSets()
    {
			$queryBuilder = $this->createQueryBuilder('gs');
			$queryBuilder->select('gs','g')
			   			 ->leftJoin('gs.gears', 'g');

			return $queryBuilder->getQuery()->getResult();
    }
}
