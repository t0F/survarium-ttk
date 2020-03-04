<?php
// src/Service/EquipmentService.php
namespace App\Service;

use App\Entity\Equipment;
use App\Entity\GameVersion;
use Doctrine\ORM\EntityManagerInterface;


class EquipmentService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function makeNewEquipement(array $equipmentsArray, GameVersion $version, array $allLocales)
    {
        foreach ($equipmentsArray as $name => $stats) {
            $equipment = new equipment();
            $equipment->setName($name);
            $equipment->setArmor($stats['parameters']['armor']);
            $equipment->setDictId($stats['parameters']['dict_id']);
            $equipment->setType($stats['parameters']['type']);
            $equipment->setGameId($stats['parameters']['id']);
            $equipment->setGameVersion($version);

            $this->translateEquipmentName($equipment, $allLocales, $stats['ui_desc']['text_descriptions']['name']);

            $this->em->persist($equipment);
            $equipment->mergeNewTranslations();
        }
        $this->em->flush();
        return true;
    }

    public function translateEquipmentName(Equipment &$equipment, array $allLocales, string $stringToFind) {
        foreach ($allLocales as $localeName => $locales) {
            if(array_key_exists ($stringToFind, $locales) && $locales[$stringToFind] != 'ОПИСАНИЕ НЕ ГОТОВО!!!') {
                $equipment->translate($localeName)->setLocalizedName($locales[$stringToFind]);
            }
        }

        if(array_key_exists ($stringToFind, $allLocales['ba'])) {
            $equipment->setName($allLocales['ba'][$stringToFind]);
        }
    }
}