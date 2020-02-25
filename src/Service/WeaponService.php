<?php
// src/Service/WeaponService.php
namespace App\Service;

use App\Entity\Equipment;
use App\Entity\GameVersion;
use App\Entity\Weapon;
use Doctrine\ORM\EntityManagerInterface;


class WeaponService
{
    private $em;
    private $sampleEquipment;
    private $sampleBonusArmor;
    private $sampleOnyx;
    private $sampleRange;
    private $sampleVersion;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->sampleRange = 1;
        $this->sampleOnyx = 0;
        $this->sampleBonusArmor = 5;
        $sampleRepo = $this->em->getRepository('App:GameVersion');
        $this->sampleVersion = $sampleRepo->findOneBy([], ['date' => 'DESC']);
        $equipmentRepo = $this->em->getRepository('App:Equipment');
        $this->sampleEquipment = $equipmentRepo->findOneBy(['name' => 'renesanse_torso_10', 'gameVersion' => $this->sampleVersion]);
    }

    public function setSample($sample)
    {
        if ($sample !== null) {
            $this->sampleEquipment = $sample['equipment'];
            $this->sampleVersion = $sample['version'];
            $this->sampleRange = $sample['range'];
            $this->sampleBonusArmor = $sample['bonusArmor'];
            $this->sampleOnyx = $sample['onyxPass'];
            if($sample['onyxAct'] > $this->sampleOnyx) {
                $this->sampleOnyx = $sample['onyxAct'];
            }
        }
    }

    public function getSampleMessage()
    {
        //"Sample is base on ZUBR body armor, with +5 amor, no onyx, no skills armor bonus, point blank.";
        return "Sample is base on " . $this->sampleEquipment->getName() . ", with +" . $this->sampleBonusArmor . " armor, " . (($this->sampleOnyx == 0) ? 'no' : $this->sampleOnyx . '%') . ' onyx, no skills armor bonus, ' . $this->sampleRange . 'm range.';
    }


    public function makeNewWeapons(array $weaponsArray, GameVersion $version)
    {
        foreach ($weaponsArray as $name => $stats) {
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
            $weapon->setMaterialPierce($stats['material_pierce']);
            $weapon->setGameVersion($version);

            $this->em->persist($weapon);
        }
        $this->em->flush();
        return true;
    }

    public function weaponsToArray($weapons)
    {
        $weaponsArray = [];

        foreach ($weapons as $weapon) {
            $weaponArray = [];
            $weaponArray['Name'] = $weapon->getFormattedName();
            $weaponArray['Type'] = $weapon->getType();
            $weaponArray['Damage'] = 100 * $weapon->getBulletDamage();
            $weaponArray['Armor Penetration'] = 100 * $weapon->getPlayerPierce();
            $weaponArray['Rate of Fire'] = $weapon->getRoundsPerMinute();
            $weaponArray['Effective Range'] = $weapon->getEffectiveDistance();
            $weaponArray['Magazine Size'] = $weapon->getMagazineCapacity();
            $weaponArray['Bleed Chance'] = 100 * $weapon->getBleedingChance();
            $weaponArray['Material Penetration'] = $weapon->getMaterialPierce();
            $weaponArray['Weight'] = $weapon->getWeight();
            $weaponArray['Reload Time'] = $weapon->getReloadTime();
            $weaponArray['Muzzle Velocity'] = $weapon->getBulletSpeed();
            $weaponArray['Sample Body Damage'] = round($this->getSampleDamage($weapon), 2);
            $weaponArray['Sample Bullets To Kill'] = $this->getSampleBTK($weapon);
            $weaponArray['Sample TimeToKill'] = $this->getSampleTimeToKill($weapon);
            $weaponArray['id'] = $weapon->getId();
            $weaponsArray[] = $weaponArray;
        }

        return $weaponsArray;
    }

    public function getRangeRatio(Weapon $weapon)
    {
        if ($weapon->getEffectiveDistance() > $this->sampleRange) {
            return 1;
        } elseif ($this->sampleRange > $weapon->getIneffectiveDistance()) {
            return $weapon->getIneffectiveDistanceDamageFactor();
        } else {
            $damageRange = $this->sampleRange - $weapon->getEffectiveDistance();
            $ratioRange = $damageRange / ($weapon->getIneffectiveDistance() - $weapon->getEffectiveDistance());

            return 1 - ((1 - $weapon->getIneffectiveDistanceDamageFactor()) * $ratioRange);
        }
    }

    public function getSampleDamage(Weapon $weapon)
    {
        $armor = $this->sampleEquipment->getArmor() * ($this->sampleBonusArmor / 100);
        return $this->getRangeRatio($weapon) * (100 * $weapon->getBulletDamage() * (1 - ($armor - $weapon->getPlayerPierce())));
    }

    public function getSampleBTK(Weapon $weapon)
    {
        return ceil(100 / $this->getSampleDamage($weapon));
    }

    public function getSampleTimeToKill(Weapon $weapon)
    {
        return round(($this->getSampleBTK($weapon) - 1) * 1 / ($weapon->getRoundsPerMinute() / 60), 3);
    }

    public function getArmorDamage(Weapon $weapon, Equipment $equipment)
    {
        // todo add onix, skills, modifier,range
        $ratioWeapon = 1;
        if ($equipment->getFormattedType() == 'HLMT' || $equipment->getFormattedType() == 'MASK')
            $ratioWeapon = 3;
        elseif ($equipment->getFormattedType() == 'BOOT')
            $ratioWeapon = 0.8;

        return $ratioWeapon * (100 * $weapon->getBulletDamage() * (1 - ($equipment->getArmor() - $weapon->getPlayerPierce())));
    }

    public function getArmorBTK(Weapon $weapon, Equipment $equipment)
    {
        return ceil(100 / $this->getArmorDamage($weapon, $equipment));
    }

    public function getArmorTimeToKill(Weapon $weapon, Equipment $equipment)
    {
        return round(($this->getArmorBTK($weapon, $equipment) - 1) * 1 / ($weapon->getRoundsPerMinute() / 60), 3);
    }

    public function weaponTTKToArray(Weapon $weapon)
    {
        $gearSetRepo = $this->em->getRepository('App:GearSet');
        $gearSets = $gearSetRepo->getGearSets();

        $formatGearSet = array();
        foreach ($gearSets as $gearSet) {
            $formatGearSet[$gearSet->getFormattedName()] = array();
            $formattedEquipment = array();
            foreach ($gearSet->getGears() as $equipment) {/*tors,boot,hlmt,hand,legs,mask,*/
                $formattedEquipment[$equipment->getFormattedType()] = array();
                $formattedEquipment[$equipment->getFormattedType()]['Bulllets To Kill'] = $this->getArmorBTK($weapon, $equipment);
                $formattedEquipment[$equipment->getFormattedType()]['Time To Kill'] = $this->getArmorTimeToKill($weapon, $equipment);
            }
            $formatGearSet[$gearSet->getFormattedName()] = $formattedEquipment;
        }

        return $formatGearSet;
    }
}