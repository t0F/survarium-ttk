<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameVersionRepository")
 */
class GameVersion
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
     * @ORM\Column(type="datetime")
     */
    private $Date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Weapon", mappedBy="gameVersion", orphanRemoval=true)
     */
    private $weapons;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GearSet", mappedBy="gameVersion", orphanRemoval=true)
     */
    private $GearSets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Equipment", mappedBy="gameVersion", orphanRemoval=true)
     */
    private $equipments;

    public function __construct()
    {
        $this->weapons = new ArrayCollection();
        $this->GearSets = new ArrayCollection();
        $this->equipments = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|Weapon[]
     */
    public function getWeapons(): Collection
    {
        return $this->weapons;
    }

    public function addWeapon(Weapon $weapon): self
    {
        if (!$this->weapons->contains($weapon)) {
            $this->weapons[] = $weapon;
            $weapon->setGameVersion($this);
        }

        return $this;
    }

    public function removeWeapon(Weapon $weapon): self
    {
        if ($this->weapons->contains($weapon)) {
            $this->weapons->removeElement($weapon);
            // set the owning side to null (unless already changed)
            if ($weapon->getGameVersion() === $this) {
                $weapon->setGameVersion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GearSet[]
     */
    public function getGearSets(): Collection
    {
        return $this->GearSets;
    }

    public function addGearSet(GearSet $gearSet): self
    {
        if (!$this->GearSets->contains($gearSet)) {
            $this->GearSets[] = $gearSet;
            $gearSet->setGameVersion($this);
        }

        return $this;
    }

    public function removeGearSet(GearSet $gearSet): self
    {
        if ($this->GearSets->contains($gearSet)) {
            $this->GearSets->removeElement($gearSet);
            // set the owning side to null (unless already changed)
            if ($gearSet->getGameVersion() === $this) {
                $gearSet->setGameVersion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Equipment[]
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments[] = $equipment;
            $equipment->setGameVersion($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        if ($this->equipments->contains($equipment)) {
            $this->equipments->removeElement($equipment);
            // set the owning side to null (unless already changed)
            if ($equipment->getGameVersion() === $this) {
                $equipment->setGameVersion(null);
            }
        }

        return $this;
    }
    
}
