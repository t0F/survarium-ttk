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
    /** @var Equipment $sampleEquipment */
    private $sampleEquipment;
    private $sampleBonusArmor;
    private $sampleOnyx;
    private $sampleRange;
    private $sampleVersion;
    private $locale;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->sampleRange = 1;
        $this->sampleOnyx = 0;
        $this->sampleBonusArmor = 5;
        $this->sampleBonusROF = 5;
        $sampleRepo = $this->em->getRepository('App:GameVersion');
        $this->sampleVersion = $sampleRepo->findOneBy([], ['date' => 'DESC']);
        $equipmentRepo = $this->em->getRepository('App:Equipment');
        $this->sampleEquipment = $equipmentRepo->findOneBy(['name' => 'renesanse_torso_10', 'gameVersion' => $this->sampleVersion]);
    }

    public function setSample($sample)
    {
        if ($sample !== null) {
            /** @var Equipment sampleEquipment */
            $this->sampleEquipment = $sample['equipment'];
            $this->sampleVersion = $sample['version'];
            $this->sampleRange = $sample['range'];
            $this->sampleBonusROF = $sample['bonusROF'];
            $this->sampleBonusArmor = $sample['bonusArmor'];
            $this->sampleOnyx = $sample['onyx'];
        }
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
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
            . $this->sampleEquipment->getName() . " ("
            . $ratioWeapon * 100 . '% Damage), '
            .'+'.$this->sampleBonusROF . '% Rate of Fire, '
            . $this->sampleEquipment->getArmor() * 100 . " + " . $this->sampleBonusArmor . " Armor, "
            . (($this->sampleOnyx == 0) ? 'no' : $this->sampleOnyx . '%') . ' Onyx, no skills Armor bonus, '
            . $this->sampleRange . 'm Range.';
    }


    public function makeNewWeapons(array $weaponsArray, GameVersion $version, array $allLocales, $modifications)
    {
        foreach ($weaponsArray as $name => $fullStats) {
            $stats = $fullStats['parameters'];
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
            $weapon->setDisplayType($this->displayType($stats['type'], $stats['magazine_capacity'], $name));

            if(isset($stats['default_modifications']) && $stats['default_modifications'] != null) {

                foreach ($stats['default_modifications'] as $weaponModification ) {
                    if(isset($modifications[$weaponModification[0].'_'.$weaponModification[1]])) {
                        $modification = $modifications[$weaponModification[0].'_'.$weaponModification[1]];
                        foreach ($modification['modifiers'] as $modifier ) {
                            if($modifier['path'][1] == 'bullet_damage') {
                                $weapon->setBulletDamage($weapon->getBulletDamage() + ($weapon->getBulletDamage() * $modifier['value']));
                            }
                        }
                    }
                }
            }

            $this->translateWeaponName($weapon, $allLocales, $fullStats['ui_desc']['text_descriptions']['name']);

            $this->em->persist($weapon);
            $weapon->mergeNewTranslations();
        }
        $this->em->flush();
        return true;
    }

    public function translateWeaponName(Weapon &$weapon, array $allLocales, string $stringToFind) {
        foreach ($allLocales as $localeName => $locales) {
            if(array_key_exists ($stringToFind, $locales)) {
                $weapon->translate($localeName)->setLocalizedName($locales[$stringToFind]);
            }
        }

        if(array_key_exists ($stringToFind, $allLocales['ba'])) {
            $weapon->setName($allLocales['ba'][$stringToFind]);
        } elseif(array_key_exists ($stringToFind, $allLocales['en'])) {
            $weapon->setName($allLocales['en'][$stringToFind]);
        }
    }

    public function getWeaponsStats($survariumPro = false)
    {
        $versionRepo = $this->em->getRepository('App:GameVersion');
        $version = $versionRepo->findOneBy(array(), array('id' => 'DESC'));
        $weaponRepo = $this->em->getRepository('App:Weapon');
        $weaponsEnt = $weaponRepo->findByGameVersion($version);
        return $this->weaponsToArray($weaponsEnt, $survariumPro);
    }

    public function weaponsToArray($weapons, $survariumPro = false)
    {
        $weaponsArray = [];

        if($survariumPro === true) {
            /** @var Weapon $weapon */
            foreach ($weapons as $weapon) {
                $weaponArray = [];
                $name = str_replace("'", "",str_replace('"', "",
                    ($weapon->translate($this->locale)->getLocalizedName() !== null) ?
                        $weapon->translate($this->locale)->getLocalizedName()
                        : strtoupper(str_replace('_', ' ', $weapon->getName()))
                ));
                $weaponArray['Sample TimeToKill'] = round($this->getArmorTimeToKill($weapon, $this->sampleEquipment),2);
                $weaponArray['Name'] = $name;
                $weaponArray['Sample Bullets To Kill'] = $this->getArmorBTK($weapon, $this->sampleEquipment);
                $weaponArray['Sample Damage'] = round($this->getArmorDamage($weapon, $this->sampleEquipment),2);
                $weaponArray['Type'] = $weapon->getDisplayType();
                $weaponArray['Damage'] = 100 * $weapon->getBulletDamage();
                $weaponArray['Armor Penetration'] = round(100 * $weapon->getPlayerPierce());
                $weaponArray['Rate of Fire'] = round($this->getROFWithBonus($weapon));
                $weaponArray['DPS'] = round($this->getDPS($weapon));
                $weaponArray['Effective Range'] = $weapon->getEffectiveDistance();
                $weaponArray['Magazine Size'] = $weapon->getMagazineCapacity();
                $weaponArray['Bleed Chance'] = round(100 * $weapon->getBleedingChance());
                $weaponArray['Material Penetration'] = $weapon->getMaterialPierce();
                $weaponArray['Weight'] = $weapon->getWeight();
                $weaponArray['Reload Time'] = $weapon->getReloadTime();
                $weaponArray['Muzzle Velocity'] = $weapon->getBulletSpeed();

                $weaponArray['id'] = $weapon->getId();
                $weaponsArray[] = $weaponArray;
            }
        } else {
            /** @var Weapon $weapon */
            foreach ($weapons as $weapon) {
                $weaponArray = [];
                $name = str_replace("'", "",str_replace('"', "",
                    ($weapon->translate($this->locale)->getLocalizedName() !== null) ?
                        $weapon->translate($this->locale)->getLocalizedName()
                        : strtoupper(str_replace('_', ' ', $weapon->getName()))
                ));
                $weaponArray['Name'] = $name;
                $weaponArray['Type'] = $weapon->getDisplayType();
                $weaponArray['Damage'] = 100 * $weapon->getBulletDamage();
                $weaponArray['Armor Penetration'] = round(100 * $weapon->getPlayerPierce());
                $weaponArray['Rate of Fire'] = round($this->getROFWithBonus($weapon));
                $weaponArray['DPS'] = round($this->getDPS($weapon));
                $weaponArray['Effective Range'] = $weapon->getEffectiveDistance();
                $weaponArray['Magazine Size'] = $weapon->getMagazineCapacity();
                $weaponArray['Bleed Chance'] = round(100 * $weapon->getBleedingChance());
                $weaponArray['Material Penetration'] = $weapon->getMaterialPierce();
                $weaponArray['Weight'] = $weapon->getWeight();
                $weaponArray['Reload Time'] = $weapon->getReloadTime();
                $weaponArray['Muzzle Velocity'] = $weapon->getBulletSpeed();
                $weaponArray['Sample Damage'] = round($this->getArmorDamage($weapon, $this->sampleEquipment),2);
                $weaponArray['Sample Bullets To Kill'] = $this->getArmorBTK($weapon, $this->sampleEquipment);
                $weaponArray['Sample TimeToKill'] = round($this->getArmorTimeToKill($weapon, $this->sampleEquipment),2);
                $weaponArray['id'] = $weapon->getId();
                $weaponsArray[] = $weaponArray;
            }
        }



        return $weaponsArray;
    }

    public function getArmorDamage(Weapon $weapon, Equipment $equipment)
    {
        $ratioWeapon = 1;        // body part ratio
        if ($equipment->getType() == 'HLMT' || $equipment->getFormattedType() == 'MASK')
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

        return $armor * $ratioWeapon * $range * $onyx * $weapon->getBulletDamage() * 100;
    }

    public function getArmorBTK(Weapon $weapon, Equipment $equipment)
    {
        return ceil(100 / $this->getArmorDamage($weapon, $equipment));
    }

    public function getROFWithBonus(Weapon $weapon)
    {
        $bonus = 1 + ($this->sampleBonusROF / 100);
        return $bonus * $weapon->getRoundsPerMinute();
    }

    public function getDPS(Weapon $weapon)
    {
        return ($this->getROFWithBonus($weapon) * ($weapon->getBulletDamage() * 100)) / 60;
    }


    public function getArmorTimeToKill(Weapon $weapon, Equipment $equipment)
    {
        return round(($this->getArmorBTK($weapon, $equipment) - 1)
            * 1 / ( $this->getROFWithBonus($weapon) / 60), 3);
    }


    public function displayType($type, $magazine, $name)
    {
        switch ($type) {
            case 'wpn_aslt':
                if(stripos($name, 'mp7') !== false
                    || stripos($name, 'ppsh') !== false
                    || stripos($name, 'mp5') !== false ) {
                    $displayType = 'SMG';
                    break;
                }

                if(stripos($name, 'icicle') !== false) {
                    $displayType = 'SPECIAL';
                    break;
                }

                if ($magazine > 40) {
                    $displayType = 'MACHINE GUNS';
                    break;
                }
                $displayType = 'ASSAULT RIFLE';
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
                if(stripos($name, 'snowball') !== false) {
                    $displayType = 'SPECIAL';
                    break;
                }
                $displayType = 'SHOTGUN';
                break;
            case 'wpn_bstgn':
                $displayType = 'SLUG';
                break;
            default:
                $displayType = 'TYPE UNKNOWN';
                break;
        }
        return $displayType;
    }
}