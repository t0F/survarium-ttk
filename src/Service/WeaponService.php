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
    private $sampleBonusRange;
    private $sampleOnyx;
    private $sampleRange;
    private $sampleVersion;
    private $showSpecial;
    private $useSilencer;
    private $sampleBonusROF;
    private $locale;

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->sampleRange = 1;
        $this->sampleOnyx = 0;
        $this->sampleBonusArmor = true;
        $this->sampleBonusRange = true;
        $this->sampleBonusROF = true;
        $this->showSpecial = false;
        $this->useSilencer = true;
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
            $this->sampleBonusRange = $sample['bonusRange'];
            $this->sampleOnyx = $sample['onyx'];
            $this->showSpecial = $sample['showSpecial'];
            $this->useSilencer = $sample['useSilencer'];
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
        $message = $this->translator->trans('Sample is base on'). ' '
            . $this->sampleEquipment->translate($this->locale)->getLocalizedName() . " ("
            . $ratioWeapon * 100 . '% ' . $this->translator->trans('damage').'), ';

        if($this->sampleBonusROF === true) {
            $message .= '+5% '.$this->translator->trans('Rate of Fire').', ';
        }

        if($this->sampleBonusArmor === true) {
            $message .= $this->sampleEquipment->getArmor() * 100 . " + 5 ". $this->translator->trans('Armor'). ", ";
        } else {
            $message .= $this->sampleEquipment->getArmor() * 100 . " ". $this->translator->trans('Armor'). ", ";
        }

        if($this->sampleBonusRange === true) {
            $message .= "+15% ".$this->translator->trans('Effective Range').", ";
        }

        $message .= (($this->sampleOnyx == 0) ? 'no ' : $this->sampleOnyx . '% ') .$this->translator->trans('Onyx')
            . ', ' . $this->translator->trans('no skills Armor bonus') . ', '
            . $this->sampleRange . 'm '. $this->translator->trans('Range.');

        return $message;
    }


    public function makeNewWeapons(array $weaponsArray, GameVersion $version, array $allLocales, $modifications, $weaponsModules)
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
            $weapon->setShotsParams(json_encode($stats['aim_recoil']));

            //modifications
            if(isset($stats['default_modifications']) && $stats['default_modifications'] != null) {
                foreach ($stats['default_modifications'] as $weaponModification ) {
                    if(isset($modifications[$weaponModification[0].'_'.$weaponModification[1]])) {
                        $modification = $modifications[$weaponModification[0].'_'.$weaponModification[1]];
                        foreach ($modification['modifiers'] as $modifier ) {
                            if($modifier['path'][1] == 'bullet_damage') {
                                $weapon->setBulletDamage($weapon->getBulletDamage() + ($weapon->getBulletDamage() * $modifier['value']));
                            }
                            if($modifier['path'][1] == 'magazine_capacity') {
                                $weapon->setMagazineCapacity($modifier['value']);
                            }

                            if($modifier['path'][1] == 'unmasking_radius') {
                                $radius = $weapon->getUnmaskingRadius();
                                $radius = $radius * (1 + $modifier['value']);
                                $weapon->setUnmaskingRadius($radius);
                            }
                        }
                    }
                }
            }
            $weapon->setRofModifier(0);
            $weapon->setSilencerModifier(0);

            //modules
            if(isset($weaponsModules[$name])) {
                $weaponCatUse = [];
                foreach($weaponsModules[$name] as $weaponModule) {
                    if(!in_array($weaponModule['category'], $weaponCatUse)) {
                        if($weaponModule['modifier'] == 'effective_distance') {
                            $weaponCatUse[] = $weaponModule['category'];
                            $weapon->setSilencerModifier($weaponModule['modifierValue']);
                        }
                        if($weaponModule['modifier'] == 'rounds_per_minute') {
                            $weaponCatUse[] = $weaponModule['category'];
                            $weapon->setRofModifier( $weapon->getRofModifier() + $weaponModule['modifierValue']);
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
                $nbBtK = $this->getArmorBTK($weapon, $this->sampleEquipment);
                $weaponArray[$this->translator->trans('sample bullets to kill')] = $nbBtK;
                $weaponArray[$this->translator->trans('sample damage')] = round($this->getArmorDamage($weapon, $this->sampleEquipment),2);
                $weaponArray[$this->translator->trans('type')] = $weapon->getDisplayType();
                $weaponArray[$this->translator->trans('sample avg.  accuracy')] = $this->getAvgAccuracy($weapon, $nbBtK);
                $weaponArray[$this->translator->trans('damage')] = round(100 * $weapon->getBulletDamage(),2);
                $weaponArray[$this->translator->trans('armor penetration')] = round(100 * $weapon->getPlayerPierce());
                $weaponArray[$this->translator->trans('rate of fire')] = round($this->getROFWithBonus($weapon));
                $weaponArray[$this->translator->trans('dps')] = round($this->getDPS($weapon));
                $weaponArray[$this->translator->trans('effective range')] = $this->getBonusEffectiveRange($weapon);
                $weaponArray[$this->translator->trans('magazine size')] = $weapon->getMagazineCapacity();
                $weaponArray[$this->translator->trans('bleed chance')] = round(100 * $weapon->getBleedingChance());
                $weaponArray[$this->translator->trans('material penetration')] = $weapon->getMaterialPierce();
                $weaponArray[$this->translator->trans('weight')] = $weapon->getWeight();
                $weaponArray[$this->translator->trans('reload time')] = $weapon->getReloadTime();
                $weaponArray[$this->translator->trans('muzzle velocity')] = $weapon->getBulletSpeed();
                $weaponArray[$this->translator->trans('unmasking radius')] = $weapon->getUnmaskingRadius();
                $weaponArray[$this->translator->trans('aimed movement speed factor')] = $weapon->getAimedMovementSpeedFactor();
                $weaponArray[$this->translator->trans('melee time')] = $weapon->getMeleeTime();
                $weaponArray[$this->translator->trans('show time')] = $weapon->getShowTime();
                $weaponArray[$this->translator->trans('aim time')] = $weapon->getAimTime();
                $weaponArray[$this->translator->trans('hide time')] = $weapon->getHideTime();
                $weaponArray[$this->translator->trans('stamina damage')] = $weapon->getStaminaDamage();
                $weaponArray[$this->translator->trans('player pierce')] = $weapon->getPlayerPierce();
                $weaponArray[$this->translator->trans('player pierced damage factor')] = $weapon->getPlayerPiercedDamageFactor();
                $weaponArray[$this->translator->trans('chamber a round time')] = $weapon->getChamberARoundTime();
                $weaponArray[$this->translator->trans('tactical reload time')] = $weapon->getTacticalReloadTime();

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
                $nbBtK = $this->getArmorBTK($weapon, $this->sampleEquipment);
                $weaponArray[$this->translator->trans('type')] = $this->translator->trans($weapon->getDisplayType());
                $weaponArray[$this->translator->trans('damage')] = round(100 * $weapon->getBulletDamage(), 2);
                $weaponArray[$this->translator->trans('armor penetration')] = round(100 * $weapon->getPlayerPierce());
                $weaponArray[$this->translator->trans('rate of fire')] = round($this->getROFWithBonus($weapon));
                $weaponArray[$this->translator->trans('dps')] = round($this->getDPS($weapon));
                $weaponArray[$this->translator->trans('effective range')] = $this->getBonusEffectiveRange($weapon);
                $weaponArray[$this->translator->trans('magazine size')] = $weapon->getMagazineCapacity();
                $weaponArray[$this->translator->trans('bleed chance')] = round(100 * $weapon->getBleedingChance());
                $weaponArray[$this->translator->trans('material penetration')] = $weapon->getMaterialPierce();
                $weaponArray[$this->translator->trans('weight')] = $weapon->getWeight();
                $weaponArray[$this->translator->trans('reload time')] = $weapon->getReloadTime();
                $weaponArray[$this->translator->trans('muzzle velocity')] = $weapon->getBulletSpeed();
                $weaponArray[$this->translator->trans('sample avg.  accuracy')] = $this->getAvgAccuracy($weapon, $nbBtK);
                $weaponArray[$this->translator->trans('sample damage')] = round($this->getArmorDamage($weapon, $this->sampleEquipment),2);
                $weaponArray[$this->translator->trans('sample bullets to kill')] = $nbBtK;
                $weaponArray[$this->translator->trans('sample timetokill')] = round($this->getArmorTimeToKill($weapon, $this->sampleEquipment),2);
                $weaponArray['id'] = $weapon->getId();
                $weaponsArray[] = $weaponArray;
            }
        }

        return $weaponsArray;
    }

    public function getBonusEffectiveRange(Weapon $weapon) {
        if(($this->useSilencer === true || $this->useSilencer === 1)
            && ($this->sampleBonusRange === true || $this->sampleBonusRange === 1)
        ) {
            return round($weapon->getEffectiveDistance() * (1 + $weapon->getSilencerModifier() + 0.15));
        } elseif($this->useSilencer === true || $this->useSilencer === 1) {
            return round($weapon->getEffectiveDistance() * (1 + $weapon->getSilencerModifier()));
        } elseif($this->sampleBonusRange === true || $this->sampleBonusRange === 1) {
            return round($weapon->getEffectiveDistance() * (1 + 0.15));
        } else {
            return $weapon->getEffectiveDistance();
        }
    }

    public function getAvgAccuracy(Weapon $weapon, $nbBtK) {
        $shotsParam = json_decode($weapon->getShotsParams());
        $allShotsAccuracies = [];

        if($nbBtK > $weapon->getMagazineCapacity()
        || $weapon->getDisplayType() == 'SHOTGUN'
            || $weapon->getDisplayType() == 'SHOTGUN'
            || $weapon->getDisplayType() == 'SPECIAL') {
            return '-';
        }

        $max = $shotsParam->shots_params[0][2];
        $min = 0;
        for ($i = 1; $i <= $nbBtK; $i++) {
            if(!isset($shotsParam->shots_params[$i-1])) {
                return '-';
            }
            $allShotsAccuracies[] = $shotsParam->shots_params[$i-1][2]; //0 recoil power, 1 recoil angle, 2 accuracy
            $min = $shotsParam->shots_params[$i-1][2];
        }
        $avg = round($shotsParam->standing_stand_accuracy * (array_sum($allShotsAccuracies) / count($allShotsAccuracies)), 1);
        $min = round($shotsParam->standing_stand_accuracy * ($min), 1);
        $max= round($shotsParam->standing_stand_accuracy * ($max), 1);
        if($min == $avg) {
            return $avg;
        }
        return $max . ' - ' . $avg . ' - ' . $min;
    }

    public function getArmorDamage(Weapon $weapon, Equipment $equipment)
    {
        $ratioWeapon = 1;        // body part ratio
        if ($equipment->getType() == 'HLMT' || $equipment->getFormattedType() == 'MASK')
            $ratioWeapon = 3;
        elseif ($equipment->getFormattedType() == 'BOOT')
            $ratioWeapon = 0.8;

        // range ratio
        if ($this->getBonusEffectiveRange($weapon) >= $this->sampleRange)
            $range = 1;
        elseif (($this->getBonusEffectiveRange($weapon) * 2) <= $this->sampleRange) {
            $range = $weapon->getIneffectiveDistanceDamageFactor();
        } else {
            $damageRange = $this->sampleRange - $this->getBonusEffectiveRange($weapon);
            $rangeReductionRatio = $damageRange / (($this->getBonusEffectiveRange($weapon) * 2) - $this->getBonusEffectiveRange($weapon));
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
        $bonusRof = ($this->sampleBonusROF === true) ? 0.05 : 0;
        if($weapon->getName() == 'Glock 17 "Legend"') {
            $bonusRof += 1.25;
        }
        $bonus = 1 + ($weapon->getRofModifier() + $bonusRof);
        if($weapon->getChamberARoundTime() == 0) {
            return $bonus * $weapon->getRoundsPerMinute();
        }
        $rof = $bonus * $weapon->getRoundsPerMinute();
        $realRof = 60 / ( (60 / $rof) + $weapon->getChamberARoundTime());
        if(round($realRof != round($realRof))) {
            $realRof = floor($realRof);
        }
        return $realRof;
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