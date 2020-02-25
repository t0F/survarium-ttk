<?php
// src/Service/GearSetService.php
namespace App\Service;

use App\Entity\GearSet;
use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;


class GearSetService
{
	 private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
	
	
    public function makeNewGearSet(array $gearsetArray)
    {
    	$equipmentRepository = $this->em->getRepository('App:Equipment');
    	foreach ($gearsetArray as $name => $stats){
			$gearset = new gearSet();
			$gearset->setName($name);
			foreach ($stats['items'] as $idEquipment){
				$equipmentToAdd = $equipmentRepository->findOneByDictId($idEquipment);
				$gearset->addGear($equipmentToAdd);
			}		

			$this->em->persist($gearset);   
		}
		$this->em->flush();
      return true;
    }
}