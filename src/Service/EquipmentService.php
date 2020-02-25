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
	
	
    public function makeNewEquipement(array $equipmentsArray, GameVersion $version)
    {
    	foreach ($equipmentsArray as $name => $stats){
			$equipment = new equipment();
			$equipment->setName($name);
			$equipment->setArmor($stats['armor']);
			$equipment->setDictId($stats['dict_id']);
			$equipment->setType($stats['type']);
			$equipment->setGameId($stats['id']);
			$equipment->setGameVersion($version);
			
			$this->em->persist($equipment);
         
		}
		$this->em->flush();
      return true;
    }
}