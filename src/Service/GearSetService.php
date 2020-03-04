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


    public function makeNewGearSet(array $gearsetArray, GameVersion $version, array $allLocales)
    {
        $equipmentRepository = $this->em->getRepository('App:Equipment');
        foreach ($gearsetArray as $name => $fullStats) {
            $gearset = new gearSet();
            $gearset->setName($this->getLocaleName($allLocales, 'st_'.$name.'_name'));
            $gearset->setGameVersion($version);
            foreach ($fullStats['items'] as $idEquipment) {
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

    public function getLocaleName(array $allLocales, string $stringToFind) {
        if(array_key_exists ($stringToFind, $allLocales['ba'])) {
            return $allLocales['ba'][$stringToFind];
        } else {
            return $stringToFind;
        }
    }
}