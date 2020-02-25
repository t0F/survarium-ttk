<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GearSetRepository")
 */
class GearSet
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
     * @ORM\OneToMany(targetEntity="App\Entity\Equipment", mappedBy="gearSet")
     */
    private $gears;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GameVersion", inversedBy="GearSets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gameVersion;

    public function __construct()
    {
        $this->gears = new ArrayCollection();
        $this->gameVersions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFormattedName(): ?string
    {
        return strtoupper(str_replace('_', ' ', str_replace('gear_set_', '', $this->name)));
    }

    /**
     * @return Collection|Equipment[]
     */
    public function getGears(): Collection
    {
        return $this->gears;
    }

    public function addGear(Equipment $gear): self
    {
        if (!$this->gears->contains($gear)) {
            $this->gears[] = $gear;
            $gear->setGearSet($this);
        }

        return $this;
    }

    public function removeGear(Equipment $gear): self
    {
        if ($this->gears->contains($gear)) {
            $this->gears->removeElement($gear);
            // set the owning side to null (unless already changed)
            if ($gear->getGearSet() === $this) {
                $gear->setGearSet(null);
            }
        }

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
