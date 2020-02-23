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
			$weapon->setMagazineCapacity($stats['magazine_capacity']);
			$weapon->setBulletDamage($stats['bullet_damage']);
			$weapon->setRoundsPerMinute($stats['rounds_per_minute']);
			$weapon->setBulletSpeed($stats['bullet_speed']);
			$weapon->setEffectiveDistance($stats['effective_distance']);
			$weapon->setBleedingChance($stats['bleeding_chance']);
			$weapon->setReloadTime($stats['reload_time']);
			$weapon->setWeight($stats['weight']);
			$weapon->setIneffectiveDistance($stats['ineffective_distance']);
			$weapon->setPlayerPierce($stats['player_pierce']);
			$weapon->setPlayerPiercedDamageFactor($stats['player_pierced_damage_factor']);
			$weapon->setRoundsPerMinuteModifier($stats['rounds_per_minute_modifier']);
			$weapon->setAimTime($stats['aim_time']);
			$weapon->setBreathVibrationFactor($stats['breath_vibration_factor']);
			$weapon->setIneffectiveDistanceDamageFactor($stats['ineffective_distance_damage_factor']);
			$weapon->setMovementSpeedModifier($stats['movement_speed_modifier']);
			$weapon->setAccuracyNonmovingModifier($stats['accuracy_nonmoving_modifier']);
			$weapon->setaimZoomFactor($stats['aim_zoom_factor']);
			$weapon->setUnmaskingRadius($stats['unmasking_radius']);
			$weapon->setChamberARoundTime($stats['chamber_a_round_time']);
			$weapon->setWeaponFovFactor($stats['weapon_fov_factor']);
			$weapon->setTacticalReloadTime($stats['tactical_reload_time']);
			$weapon->setAimedMovementSpeedFactor($stats['aimed_movement_speed_factor']);
			$weapon->setShowTime($stats['show_time']);
			$weapon->setStaminaDamage($stats['stamina_damage']);
			$weapon->setType($stats['type']);
			$weapon->setMeleeTime($stats['melee_time']);
			$weapon->setThrowGrenadeTime($stats['throw_grenade_time']);
			$weapon->setHideTime($stats['hide_time']);
			
			$this->em->persist($weapon);
         $this->em->flush();
		}

        return true;
    }
    
    public function weaponsToArray($weapons){
			$weaponsArray = [];

			foreach($weapons as $weapon) {
					$weaponArray = [];
					$weaponArray['Name'] = str_replace('_', ' ', $weapon->getName());
					$weaponArray['Type'] = $weapon->getType();
					$weaponArray['Rate of Fire'] = $weapon->getRoundsPerMinute();
					$weaponArray['Damage'] = 100 * $weapon->getBulletDamage();
					$weaponArray['Raw DPS'] = $this->getRawDPS($weapon);
					$weaponArray['Magazine Capacity'] = $weapon->getMagazineCapacity();
					$weaponArray['Reload Time'] = $weapon->getReloadTime();
					$weaponArray['Real DPS'] = $this->getRealDPS($weapon);
					$weaponArray['Armor Penetration'] = $weapon->getPlayerPierce();
					$weaponArray['Bullet Speed'] = $weapon->getBulletSpeed();
					$weaponArray['Effective Distance'] = $weapon->getEffectiveDistance();
					$weaponArray['Ineffective Distance'] = $weapon->getIneffectiveDistance();
					$weaponArray['Ineffective Distance Damage Factor'] = $weapon->getIneffectiveDistanceDamageFactor();
					$weaponArray['Bleeding Chance'] = $weapon->getBleedingChance();				
					$weaponArray['Weight'] = $weapon->getWeight();
					$weaponArray['Aim Time'] = $weapon->getAimTime();	
					$weaponArray['Unmasking Radius'] = $weapon->getUnmaskingRadius();
					$weaponArray['Chamber A Round Time'] = $weapon->getChamberARoundTime();
					$weaponArray['Weapon Fov Factor'] = $weapon->getWeaponFovFactor();
					$weaponArray['Tactical Reload Time'] = $weapon->getTacticalReloadTime();
					$weaponArray['Aimed Movement Speed Factor'] = $weapon->getAimedMovementSpeedFactor();
					$weaponArray['Show Time'] = $weapon->getShowTime();
					$weaponArray['Hide Time'] = $weapon->getHideTime();
					$weaponArray['Stamina Damage'] = $weapon->getStaminaDamage();
					$weaponArray['Melee Time'] = $weapon->getMeleeTime();
					$weaponArray['Throw Grenade Time'] = $weapon->getThrowGrenadeTime();
					$weaponsArray[] = $weaponArray;
			}
			return $weaponsArray;
    }
    
    public function getRawDPS(Weapon $weapon) {
    		return round(100 * $weapon->getBulletDamage() * $weapon->getRoundsPerMinute() / 60, 2);
    }
    
    public function getRealDPS(Weapon $weapon) {
    		$timePerBullet = 1 / ( $weapon->getRoundsPerMinute() / 60);
    		$timeWithReloading = ($timePerBullet * $weapon->getMagazineCapacity()) + $weapon->getReloadTime();
    		return round(((100 * $weapon->getBulletDamage()) * $weapon->getMagazineCapacity()) / $timeWithReloading, 2);
    }
}