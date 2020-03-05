<?php
// src/Service/GearSetService.php
namespace App\Service;

use App\Entity\Equipment;
use App\Entity\GameVersion;
use App\Entity\GearSet;
use Doctrine\ORM\EntityManagerInterface;


class GearSetService
{
    private $em;
    private $gearSetCreated;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function makeNewGearSet(array $gearsetArray, GameVersion $version)
    {
        $equipmentRepository = $this->em->getRepository('App:Equipment');
        foreach ($gearsetArray as $name => $stats) {
            $gearset = new gearSet();
            $gearset->setName($name);
            $gearset->setGameVersion($version);
            foreach ($stats['items'] as $idEquipment) {
                $equipmentToAdd = $equipmentRepository->findOneByDictId($idEquipment);
                $gearset->addGear($equipmentToAdd);
            }
            //save it for use it later to upload display name
            $this->gearSetCreated[] = $gearset;
            $this->em->persist($gearset);
        }
        $this->em->flush();
        return true;
    }

    //call this after creating gearsets and equipments
    public function updateEquipmentsDisplay()
    {
        /** @var GearSet $gearSet */
        foreach ($this->gearSetCreated as $gearSet) {

            /** @var Equipment $gear */
            foreach ($gearSet->getGears() as $equipment) {
                switch ($equipment->getType()) {
                    case 'arm_tors':
                        $displayType = 'VEST';
                        break;
                    case 'arm_boot':
                        $displayType = 'BOOTS';
                        break;
                    case 'arm_hlmt':
                        $displayType = 'HELMET';
                        break;
                    case 'arm_hand':
                        $displayType = 'GLOVES';
                        break;
                    case 'arm_legs':
                        $displayType = 'PANTS';
                        break;
                    case 'arm_mask':
                        $displayType = 'MASK';
                        break;
                    case 'arm_back':
                        $displayType = 'BACK';
                        break;
                    case 'arm_oxy':
                        $displayType = 'OXY';
                        break;
                    default:
                        $displayType = 'TYPE UNKNOWN';
                        break;
                }

                $displayName = $gearSet->getFormattedName() . ' ' . $displayType;
                $equipment->setDisplayType($displayType);
                $equipment->setDisplayName($displayName);
                $this->em->persist($equipment);
            }
        }
        $this->em->flush();
    }
}