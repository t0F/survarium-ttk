<?php
// src/Service/WeaponService.php
namespace App\Service;

use App\Entity\Equipment;
use App\Entity\GameVersion;
use App\Entity\Weapon;
use App\Entity\WeaponConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


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

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->sampleRange = 1;
        $this->sampleOnyx = 0;
        $this->sampleBonusArmor = 5;
        $this->sampleBonusROF = 5;
        $this->showSpecial = false;
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
            $this->showSpecial = $sample['showSpecial'];
        }
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        $this->translator->setLocale($this->locale);
    }


    public function getSampleMessage()
    {
        // body part ratio
        $ratioWeapon = 1;
        if ($this->sampleEquipment->getFormattedType() == 'HLMT' || $this->sampleEquipment->getFormattedType() == 'MASK')
            $ratioWeapon = 3;
        elseif ($this->sampleEquipment->getFormattedType() == 'BOOT')
            $ratioWeapon = 0.8;

        //Sample is base on "Zubr UM-4" bulletproof vest (100% Damage), +5% Rate of Fire, 71 + 5 Armor, 4% Onyx, no skills Armor bonus, 40m Range.
        return $this->translator->trans('Sample is base on'). ' '
            . $this->sampleEquipment->translate($this->locale)->getLocalizedName() . " ("
            . $ratioWeapon * 100 . '% ' . $this->translator->trans('damage').'), '
            .'+'.$this->sampleBonusROF . '% '.$this->translator->trans('Rate of Fire').', '
            . $this->sampleEquipment->getArmor() * 100 . " + " . $this->sampleBonusArmor
            . ' ' . $this->translator->trans('Armor'). ", "
            . (($this->sampleOnyx == 0) ? 'no ' : $this->sampleOnyx . '% ') .$this->translator->trans('Onyx')
            . ', ' . $this->translator->trans('no skills Armor bonus') . ', '
            . $this->sampleRange . 'm '. $this->translator->trans('Range');
    }


    public function makeNewWeapons(array $weaponsArray, GameVersion $version, array $allLocales, $modifications)
    {
        $weaponConfRepo = $this->em->getRepository('App:WeaponConfiguration');
        $weaponRepo = $this->em->getRepository('App:Weapon');
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
                            if($modifier['path'][1] == 'magazine_capacity') {
                                dump($modifier);
                                $weapon->setMagazineCapacity($modifier['value']);
                            }
                        }
                    }
                }
            }

            $this->translateWeaponName($weapon, $allLocales, $fullStats['ui_desc']['text_descriptions']['name']);
            $alreadyExist = $weaponRepo->findOneBy(['name' => $weapon->getName(), 'gameVersion' => $weapon->getGameVersion()]);
            if($alreadyExist === null) {
                $this->makeWeaponConfiguration($weapon);

                //Check if need to flag as special weapon (events, premium, etc)
                $weaponConf = $weaponConfRepo->findOneByName($weapon->getName());
                if($weaponConf !== null) {
                    /** @var WeaponConfiguration $weaponConf */
                    $weapon->setIsSpecial($weaponConf->getIsSpecial());
                }
                $this->em->persist($weapon);
                $this->em->flush();
            }

            $weapon->mergeNewTranslations();
        }

        $this->em->flush();
        return true;
    }

    public function makeWeaponConfiguration(Weapon $weapon) {
        $weaponConfRepo = $this->em->getRepository('App:WeaponConfiguration');
        $weaponConf = $weaponConfRepo->findOneByName($weapon->getName());

        if($weaponConf === null) {
            $weaponConf = new WeaponConfiguration();
            $weaponConf->setName($weapon->getName());

            if(preg_match('(premium|2015|2016|2017|2018|2019|2020|2021|2022|Snowball|summer|legend)', $weapon->getName()) === 1) {
                $weaponConf->setIsSpecial(true);
            } else {
                $weaponConf->setIsSpecial(false);
            }
            $this->em->persist($weaponConf);
        }
        $this->em->flush();
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
        $weaponRepo = $this->em->getRepository('App:Weapon');
        $weaponsEnt = $weaponRepo->findByGameVersionAndLocale($this->sampleVersion, $this->locale, $this->showSpecial);
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

                $weaponArray[$this->translator->trans('name')] = $name;
                $weaponArray[$this->translator->trans('sample timetokill')] = round($this->getArmorTimeToKill($weapon, $this->sampleEquipment),2);
                $weaponArray[$this->translator->trans('sample bullets to kill')] = $this->getArmorBTK($weapon, $this->sampleEquipment);
                $weaponArray[$this->translator->trans('sample damage')] = round($this->getArmorDamage($weapon, $this->sampleEquipment),2);
                $weaponArray[$this->translator->trans('type')] = $weapon->getDisplayType();
                $weaponArray[$this->translator->trans('damage')] = 100 * $weapon->getBulletDamage();
                $weaponArray[$this->translator->trans('armor penetration')] = round(100 * $weapon->getPlayerPierce());
                $weaponArray[$this->translator->trans('rate of fire')] = round($this->getROFWithBonus($weapon));
                $weaponArray[$this->translator->trans('dps')] = round($this->getDPS($weapon));
                $weaponArray[$this->translator->trans('effective range')] = $weapon->getEffectiveDistance();
                $weaponArray[$this->translator->trans('magazine size')] = $weapon->getMagazineCapacity();
                $weaponArray[$this->translator->trans('bleed chance')] = round(100 * $weapon->getBleedingChance());
                $weaponArray[$this->translator->trans('material penetration')] = $weapon->getMaterialPierce();
                $weaponArray[$this->translator->trans('weight')] = $weapon->getWeight();
                $weaponArray[$this->translator->trans('reload time')] = $weapon->getReloadTime();
                $weaponArray[$this->translator->trans('muzzle velocity')] = $weapon->getBulletSpeed();

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
                $weaponArray[$this->translator->trans('name')] = $name;
                $weaponArray[$this->translator->trans('type')] = $this->translator->trans($weapon->getDisplayType());
                $weaponArray[$this->translator->trans('damage')] = 100 * $weapon->getBulletDamage();
                $weaponArray[$this->translator->trans('armor penetration')] = round(100 * $weapon->getPlayerPierce());
                $weaponArray[$this->translator->trans('rate of fire')] = round($this->getROFWithBonus($weapon));
                $weaponArray[$this->translator->trans('dps')] = round($this->getDPS($weapon));
                $weaponArray[$this->translator->trans('effective range')] = $weapon->getEffectiveDistance();
                $weaponArray[$this->translator->trans('magazine size')] = $weapon->getMagazineCapacity();
                $weaponArray[$this->translator->trans('bleed chance')] = round(100 * $weapon->getBleedingChance());
                $weaponArray[$this->translator->trans('material penetration')] = $weapon->getMaterialPierce();
                $weaponArray[$this->translator->trans('weight')] = $weapon->getWeight();
                $weaponArray[$this->translator->trans('reload time')] = $weapon->getReloadTime();
                $weaponArray[$this->translator->trans('muzzle velocity')] = $weapon->getBulletSpeed();
                $weaponArray[$this->translator->trans('sample damage')] = round($this->getArmorDamage($weapon, $this->sampleEquipment),2);
                $weaponArray[$this->translator->trans('sample bullets to kill')] = $this->getArmorBTK($weapon, $this->sampleEquipment);
                $weaponArray[$this->translator->trans('sample timetokill')] = round($this->getArmorTimeToKill($weapon, $this->sampleEquipment),2);
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
                $displayType = 'PISTOLS';
                break;
            case 'wpn_dbrl':
            case 'wpn_stgn':
            case 'wpn_asgn':
            case 'wpn_bstgn':
                if(stripos($name, 'snowball') !== false) {
                    $displayType = 'SPECIAL';
                    break;
                }
                $displayType = 'SHOTGUN';
                break;
            default:
                $displayType = 'TYPE UNKNOWN';
                break;
        }
        return $displayType;
    }
}