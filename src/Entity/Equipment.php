<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EquipmentRepository")
 */
class Equipment implements TranslatableInterface
{
    use TranslatableTrait;

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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GameVersion", inversedBy="equipments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gameVersion;

    public function __construct()
    {
        $this->gameVersions = new ArrayCollection();
    }

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

    public function getFormattedType(): ?string
    {
        return strtoupper(str_replace('arm_', '', $this->type));
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

    public function getGameVersion(): ?GameVersion
    {
        return $this->gameVersion;
    }

    public function setGameVersion(?GameVersion $gameVersion): self
    {
        $this->gameVersion = $gameVersion;

        return $this;
    }
}
