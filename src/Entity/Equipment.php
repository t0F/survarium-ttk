<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EquipmentRepository")
 */
class Equipment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $armor;

    /**
     * @ORM\Column(type="integer")
     */
    private $gameId;

    /**
     * @ORM\Column(type="integer")
     */
    private $dictId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GearSet", inversedBy="gears")
     */
    private $gearSet;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArmor(): ?float
    {
        return $this->armor;
    }

    public function setArmor(float $armor): self
    {
        $this->armor = $armor;

        return $this;
    }

    public function getGameId(): ?int
    {
        return $this->gameId;
    }

    public function setGameId(int $gameId): self
    {
        $this->gameId = $gameId;

        return $this;
    }

    public function getDictId(): ?int
    {
        return $this->dictId;
    }

    public function setDictId(int $dictId): self
    {
        $this->dictId = $dictId;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGearSet(): ?GearSet
    {
        return $this->gearSet;
    }

    public function setGearSet(?GearSet $gearSet): self
    {
        $this->gearSet = $gearSet;

        return $this;
    }
}
