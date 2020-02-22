<?php
// src/Service/WeaponService.php
namespace App\Service;

use App\Entity\Weapon;
use Doctrine\ORM\EntityManagerInterface;


class WeaponService
{
	 private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
	
	
    public function makeNewWeapons(array $weaponsArray)
    {
    	foreach ($weaponsArray as $name => $stats){
			$weapon = new weapon();
			$weapon->setName($name);
			$weapon->setBleedingChance($stats['bleeding_chance']);
			$weapon->setBulletDamage($stats['bullet_damage']);
			$weapon->setBulletSpeed($stats['bullet_speed']);
			$weapon->setEffectiveDistance($stats['effective_distance']);
			$weapon->setMagazineCapacity($stats['magazine_capacity']);
			$weapon->setReloadTime($stats['reload_time']);
			$weapon->setRoundsPerMinute($stats['rounds_per_minute']);
			$weapon->setWeight($stats['weight']);
			$weapon->setIneffectiveDistance($stats['ineffective_distance']);
			$weapon->setPlayerPierce($stats['player_pierce']);
			$weapon->setPlayerPiercedDamageFactor($stats['player_pierced_damage_factor']);
			$weapon->setRoundsPerMinuteModifier($stats['rounds_per_minute_modifier']);
			
			$this->em->persist($weapon);
         $this->em->flush();
		}

        return true;
    }
}