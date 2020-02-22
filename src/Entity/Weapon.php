<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WeaponRepository")
 */
class Weapon
{
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
}
