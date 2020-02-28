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
            $this->sampleOnyx = $sample['onyx'];
        }
    }

    public function getSampleMessage()
    {
        // body part ratio
        $ratioWeapon = 1;
        if ($this->sampleEquipment->getFormattedType() == 'HLMT' || $this->sampleEquipment->getFormattedType() == 'MASK')
            $ratioWeapon = 3;
        elseif ($this->sampleEquipment->getFormattedType() == 'BOOT')
            $ratioWeapon = 0.8;

        //"Sample is base on ZUBR VEST (100% damage), with +5 armor, no onyx, no skills armor bonus, point blank.";
        return "Sample is base on "
            . $this->sampleEquipment->getDisplayName() . " ("
            . $ratioWeapon * 100 . '% damage), '
            . $this->sampleEquipment->getArmor() * 100 . " + " . $this->sampleBonusArmor . " armor, "
            . (($this->sampleOnyx == 0) ? 'no' : $this->sampleOnyx . '%') . ' onyx, no skills armor bonus, '
            . $this->sampleRange . 'm range.';
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
            $weapon->setDisplayType($this->displayType($stats['type'], $stats['magazine_capacity']));

            $this->em->persist($weapon);
        }
        $this->em->flush();
        return true;
    }

    public function getWeaponsStats()
    {
        $versionRepo = $this->em->getRepository('App:GameVersion');
        $version = $versionRepo->findOneBy(array(), array('id' => 'DESC'));
        $weaponRepo = $this->em->getRepository('App:Weapon');
        $weaponsEnt = $weaponRepo->findByGameVersion($version);
        return $this->weaponsToArray($weaponsEnt);
    }

    public function weaponsToArray($weapons)
    {
        $weaponsArray = [];
        /** @var Weapon $weapon */
        foreach ($weapons as $weapon) {
            $weaponArray = [];
            $weaponArray['Name'] = $weapon->getFormattedName();
            $weaponArray['Type'] = $weapon->getDisplayType();
            $weaponArray['Damage'] = round(100 * $weapon->getBulletDamage());
            $weaponArray['Armor Penetration'] = round(100 * $weapon->getPlayerPierce());
            $weaponArray['Rate of Fire'] = $weapon->getRoundsPerMinute();
            $weaponArray['Effective Range'] = $weapon->getEffectiveDistance();
            $weaponArray['Magazine Size'] = $weapon->getMagazineCapacity();
            $weaponArray['Bleed Chance'] = round(100 * $weapon->getBleedingChance());
            $weaponArray['Material Penetration'] = $weapon->getMaterialPierce();
            $weaponArray['Weight'] = $weapon->getWeight();
            $weaponArray['Reload Time'] = $weapon->getReloadTime();
            $weaponArray['Muzzle Velocity'] = $weapon->getBulletSpeed();
            $weaponArray['Sample Damage'] = round($this->getArmorDamage($weapon, $this->sampleEquipment));
            $weaponArray['Sample Bullets To Kill'] = $this->getArmorBTK($weapon, $this->sampleEquipment);
            $weaponArray['Sample TimeToKill'] = $this->getArmorTimeToKill($weapon, $this->sampleEquipment);
            $weaponArray['id'] = $weapon->getId();
            $weaponsArray[] = $weaponArray;
        }

        return $weaponsArray;
    }

    public function getArmorDamage(Weapon $weapon, Equipment $equipment)
    {
        $ratioWeapon = 1;        // body part ratio
        if ($equipment->getFormattedType() == 'HLMT' || $equipment->getFormattedType() == 'MASK')
            $ratioWeapon = 3;
        elseif ($equipment->getFormattedType() == 'BOOT')
            $ratioWeapon = 0.8;

        // range ratio
        if ($weapon->getEffectiveDistance() >= $this->sampleRange)
            $range = 1;
        elseif ($weapon->getIneffectiveDistance() <= $this->sampleRange) {
            $range = $weapon->getIneffectiveDistanceDamageFactor();
        } else {
            $damageRange = $this->sampleRange - $weapon->getEffectiveDistance();
            $rangeReductionRatio = $damageRange / ($weapon->getIneffectiveDistance() - $weapon->getEffectiveDistance());
            if ($rangeReductionRatio > 1) $rangeReductionRatio = 1;

            $range = 1 - (1 - $weapon->getIneffectiveDistanceDamageFactor()) * $rangeReductionRatio;
        }

        // onyx ratio (user input, passive to active)
        $onyx = 1 - ($this->sampleOnyx / 100);

        //armor ratio : armor + armor modifier - armor penetration
        $armor = 1 - (($this->sampleBonusArmor / 100) + $equipment->getArmor() - $weapon->getPlayerPierce());

        /*
                if($weapon->getFormattedName() == "A545 NEWYEAR2018") {
                    dump($weapon->getName());
                    dump($armor);
                    dump($onyx);
                    dump($range);
                    dump($ratioWeapon);
                    dump($weapon->getBulletDamage() * 100);
                    dump($armor * $onyx * $range * $ratioWeapon * ($weapon->getBulletDamage() * 100));
                 }
        */

        return $armor * $ratioWeapon * $range * $onyx * $weapon->getBulletDamage() * 100;
    }

    public function getArmorBTK(Weapon $weapon, Equipment $equipment)
    {
        return ceil(100 / $this->getArmorDamage($weapon, $equipment));
    }

    public function getArmorTimeToKill(Weapon $weapon, Equipment $equipment)
    {
        return round(($this->getArmorBTK($weapon, $equipment) - 1)
            * 1 / ($weapon->getRoundsPerMinute() / 60), 3);
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

    public function displayType($type, $magazine) {
        switch ($type) {
            case 'wpn_aslt':
                if ($magazine > 40) {
                    $displayType = 'MACHINE GUNS';
                } else {
                    $displayType = 'ASSAULT RIFLE';
                }
                break;
            case 'wpn_smg':
                $displayType = 'SMG';
                break;
            case 'wpn_bolt':
            case 'wpn_ablt':
                $displayType = 'SNIPER RIFLE';
                break;
            case 'wpn_cara':
                $displayType = 'CARBINE';
                break;
            case 'wpn_apst':
            case 'wpn_rvlr':
            case 'wpn_pstl':
                $displayType = 'GUNS';
                break;
            case 'wpn_dbrl':
            case 'wpn_stgn':
            case 'wpn_asgn':
                $displayType = 'SHOTGUN';
                break;
            case 'wpn_bstgn':
                $displayType = 'OXY';
                break;
            default:
                $displayType = 'TYPE UNKNOWN';
                break;
        }
        return $displayType;
    }
}