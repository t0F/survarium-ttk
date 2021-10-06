<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WeaponRepository")
 */
class Weapon implements TranslatableInterface
{
    use TranslatableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $roundsPerMinute;

    /**
     * @ORM\Column(type="float")
     */
    private $reloadTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $magazineCapacity;

    /**
     * @ORM\Column(type="float")
     */
    private $weight;

    /**
     * @ORM\Column(type="float")
     */
    private $bulletDamage;

    /**
     * @ORM\Column(type="float")
     */
    private $effectiveDistance;

    /**
     * @ORM\Column(type="float")
     */
    private $bleedingChance;

    /**
     * @ORM\Column(type="float")
     */
    private $bulletSpeed;

    /**
     * @ORM\Column(type="float")
     */
    private $ineffectiveDistance;

    /**
     * @ORM\Column(type="float")
     */
    private $playerPierce;

    /**
     * @ORM\Column(type="float")
     */
    private $playerPiercedDamageFactor;

    /**
     * @ORM\Column(type="integer")
     */
    private $roundsPerMinuteModifier;

    /**
     * @ORM\Column(type="float")
     */
    private $ineffectiveDistanceDamageFactor;

    /**
     * @ORM\Column(type="float")
     */
    private $aimTime;

    /**
     * @ORM\Column(type="float")
     */
    private $breathVibrationFactor;

    /**
     * @ORM\Column(type="float")
     */
    private $movementSpeedModifier;

    /**
     * @ORM\Column(type="float")
     */
    private $accuracyNonmovingModifier;

    /**
     * @ORM\Column(type="float")
     */
    private $aimZoomFactor;

    /**
     * @ORM\Column(type="float")
     */
    private $unmaskingRadius;

    /**
     * @ORM\Column(type="float")
     */
    private $chamberARoundTime;

    /**
     * @ORM\Column(type="float")
     */
    private $weaponFovFactor;

    /**
     * @ORM\Column(type="float")
     */
    private $tacticalReloadTime;

    /**
     * @ORM\Column(type="float")
     */
    private $aimedMovementSpeedFactor;

    /**
     * @ORM\Column(type="float")
     */
    private $showTime;

    /**
     * @ORM\Column(type="float")
     */
    private $staminaDamage;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     */
    private $meleeTime;

    /**
     * @ORM\Column(type="float")
     */
    private $throwGrenadeTime;

    /**
     * @ORM\Column(type="float")
     */
    private $hideTime;

    /**
     * @ORM\Column(type="float")
     */
    private $materialPierce;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GameVersion", inversedBy="weapons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gameVersion;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $displayType;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isSpecial;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $silencerModifier;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rofModifier;

    /**
     * @ORM\Column(type="string", length=15000, nullable=true)
     */
    private $shotsParams;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $hipSpeedFactor;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $icon;

    public function __construct()
    {
        $this->gearSets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoundsPerMinute(): ?int
    {
        return $this->roundsPerMinute;
    }

    public function setRoundsPerMinute(int $roundsPerMinute): self
    {
        $this->roundsPerMinute = $roundsPerMinute;

        return $this;
    }

    public function getReloadTime(): ?float
    {
        return $this->reloadTime;
    }

    public function setReloadTime(float $reloadTime): self
    {
        $this->reloadTime = $reloadTime;

        return $this;
    }

    public function getMagazineCapacity(): ?int
    {
        return $this->magazineCapacity;
    }

    public function setMagazineCapacity(int $magazineCapacity): self
    {
        $this->magazineCapacity = $magazineCapacity;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getBulletDamage(): ?float
    {
        return $this->bulletDamage;
    }

    public function setBulletDamage(float $bulletDamage): self
    {
        $this->bulletDamage = $bulletDamage;

        return $this;
    }

    public function getEffectiveDistance(): ?float
    {
        return $this->effectiveDistance;
    }

    public function setEffectiveDistance(float $effectiveDistance): self
    {
        $this->effectiveDistance = $effectiveDistance;

        return $this;
    }

    public function getBleedingChance(): ?float
    {
        return $this->bleedingChance;
    }

    public function setBleedingChance(float $bleedingChance): self
    {
        $this->bleedingChance = $bleedingChance;

        return $this;
    }

    public function getBulletSpeed(): ?float
    {
        return $this->bulletSpeed;
    }

    public function setBulletSpeed(float $bulletSpeed): self
    {
        $this->bulletSpeed = $bulletSpeed;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getFormattedName(): ?string
    {
        return strtoupper(str_replace('_', ' ', $this->name));
    }


    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIneffectiveDistance(): ?float
    {
        return $this->ineffectiveDistance;
    }

    public function setIneffectiveDistance(float $ineffectiveDistance): self
    {
        $this->ineffectiveDistance = $ineffectiveDistance;

        return $this;
    }

    public function getPlayerPierce(): ?float
    {
        return $this->playerPierce;
    }

    public function setPlayerPierce(float $playerPierce): self
    {
        $this->playerPierce = $playerPierce;

        return $this;
    }

    public function getPlayerPiercedDamageFactor(): ?float
    {
        return $this->playerPiercedDamageFactor;
    }

    public function setPlayerPiercedDamageFactor(float $playerPiercedDamageFactor): self
    {
        $this->playerPiercedDamageFactor = $playerPiercedDamageFactor;

        return $this;
    }

    public function getRoundsPerMinuteModifier(): ?int
    {
        return $this->roundsPerMinuteModifier;
    }

    public function setRoundsPerMinuteModifier(int $roundsPerMinuteModifier): self
    {
        $this->roundsPerMinuteModifier = $roundsPerMinuteModifier;

        return $this;
    }

    public function getIneffectiveDistanceDamageFactor(): ?float
    {
        return $this->ineffectiveDistanceDamageFactor;
    }

    public function setIneffectiveDistanceDamageFactor(float $ineffectiveDistanceDamageFactor): self
    {
        $this->ineffectiveDistanceDamageFactor = $ineffectiveDistanceDamageFactor;

        return $this;
    }

    public function getAimTime(): ?float
    {
        return $this->aimTime;
    }

    public function setAimTime(float $aimTime): self
    {
        $this->aimTime = $aimTime;

        return $this;
    }

    public function getBreathVibrationFactor(): ?float
    {
        return $this->breathVibrationFactor;
    }

    public function setBreathVibrationFactor(float $breathVibrationFactor): self
    {
        $this->breathVibrationFactor = $breathVibrationFactor;

        return $this;
    }

    public function getMovementSpeedModifier(): ?float
    {
        return $this->movementSpeedModifier;
    }

    public function setMovementSpeedModifier(float $movementSpeedModifier): self
    {
        $this->movementSpeedModifier = $movementSpeedModifier;

        return $this;
    }

    public function getAccuracyNonmovingModifier(): ?float
    {
        return $this->accuracyNonmovingModifier;
    }

    public function setAccuracyNonmovingModifier(float $accuracyNonmovingModifier): self
    {
        $this->accuracyNonmovingModifier = $accuracyNonmovingModifier;

        return $this;
    }

    public function getAimZoomFactor(): ?float
    {
        return $this->aimZoomFactor;
    }

    public function setAimZoomFactor(float $aimZoomFactor): self
    {
        $this->aimZoomFactor = $aimZoomFactor;

        return $this;
    }

    public function getUnmaskingRadius(): ?float
    {
        return $this->unmaskingRadius;
    }

    public function setUnmaskingRadius(float $unmaskingRadius): self
    {
        $this->unmaskingRadius = $unmaskingRadius;

        return $this;
    }

    public function getChamberARoundTime(): ?float
    {
        return $this->chamberARoundTime;
    }

    public function setChamberARoundTime(float $chamberARoundTime): self
    {
        $this->chamberARoundTime = $chamberARoundTime;

        return $this;
    }

    public function getWeaponFovFactor(): ?float
    {
        return $this->weaponFovFactor;
    }

    public function setWeaponFovFactor(float $weaponFovFactor): self
    {
        $this->weaponFovFactor = $weaponFovFactor;

        return $this;
    }

    public function getTacticalReloadTime(): ?float
    {
        return $this->tacticalReloadTime;
    }

    public function setTacticalReloadTime(float $tacticalReloadTime): self
    {
        $this->tacticalReloadTime = $tacticalReloadTime;

        return $this;
    }

    public function getAimedMovementSpeedFactor(): ?float
    {
        return $this->aimedMovementSpeedFactor;
    }

    public function setAimedMovementSpeedFactor(float $aimedMovementSpeedFactor): self
    {
        $this->aimedMovementSpeedFactor = $aimedMovementSpeedFactor;

        return $this;
    }

    public function getShowTime(): ?float
    {
        return $this->showTime;
    }

    public function setShowTime(float $showTime): self
    {
        $this->showTime = $showTime;

        return $this;
    }

    public function getStaminaDamage(): ?float
    {
        return $this->staminaDamage;
    }

    public function setStaminaDamage(float $staminaDamage): self
    {
        $this->staminaDamage = $staminaDamage;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMeleeTime(): ?float
    {
        return $this->meleeTime;
    }

    public function setMeleeTime(float $meleeTime): self
    {
        $this->meleeTime = $meleeTime;

        return $this;
    }

    public function getThrowGrenadeTime(): ?float
    {
        return $this->throwGrenadeTime;
    }

    public function setThrowGrenadeTime(float $throwGrenadeTime): self
    {
        $this->throwGrenadeTime = $throwGrenadeTime;

        return $this;
    }

    public function getHideTime(): ?float
    {
        return $this->hideTime;
    }

    public function setHideTime(float $hideTime): self
    {
        $this->hideTime = $hideTime;

        return $this;
    }

    public function getMaterialPierce(): ?float
    {
        return $this->materialPierce;
    }

    public function setMaterialPierce(float $materialPierce): self
    {
        $this->materialPierce = $materialPierce;

        return $this;
    }

    public function getGameVersion(): ?GameVersion
    {
        return $this->gameVersion;
    }

    public function setGameVersion(?GameVersion $gameVersion): self
    {
        $this->gameVersion = $gameVersion;

        return $this;
    }

    public function getDisplayType(): ?string
    {
        return $this->displayType;
    }

    public function setDisplayType(?string $displayType): self
    {
        $this->displayType = $displayType;

        return $this;
    }

    public function getIsSpecial(): ?bool
    {
        return $this->isSpecial;
    }

    public function setIsSpecial(?bool $isSpecial): self
    {
        $this->isSpecial = $isSpecial;

        return $this;
    }

    public function getSilencerModifier(): ?float
    {
        return $this->silencerModifier;
    }

    public function setSilencerModifier(?float $silencerModifier): self
    {
        $this->silencerModifier = $silencerModifier;

        return $this;
    }

    public function getRofModifier(): ?float
    {
        return $this->rofModifier;
    }

    public function setRofModifier(?float $rofModifier): self
    {
        $this->rofModifier = $rofModifier;

        return $this;
    }

    public function getShotsParams(): ?string
    {
        return $this->shotsParams;
    }

    public function setShotsParams(?string $shotsParams): self
    {
        $this->shotsParams = $shotsParams;

        return $this;
    }

    public function getHipSpeedFactor(): ?float
    {
        return $this->hipSpeedFactor;
    }

    public function setHipSpeedFactor(?float $hipSpeedFactor): self
    {
        $this->hipSpeedFactor = $hipSpeedFactor;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }
}
