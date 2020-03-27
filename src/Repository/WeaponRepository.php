<?php

namespace App\Repository;

use App\Entity\Weapon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Weapon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Weapon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Weapon[]    findAll()
 * @method Weapon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeaponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Weapon::class);
    }

    public function findByGameVersionAndLocale($version, $locale, $showSpecial) {
        $form = $this->createQueryBuilder('w')
            ->select('w', 't')
            ->leftJoin('w.translations', 't', 'WITH', 't.locale = :locale')
            ->andWhere('w.gameVersion = :lastVersion')
            ->setParameter('lastVersion', $version)
            ->setParameter('locale', $locale);
        if($showSpecial !== true) {
            $form->andWhere('w.isSpecial = false');
        }

        return $form->orderBy('w.name', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
