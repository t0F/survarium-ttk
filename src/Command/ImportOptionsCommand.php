<?php

namespace App\Command;

use App\Service\WeaponService;
use App\Service\EquipmentService;
use App\Service\GearSetService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportOptionsCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:import-options';
    
    private $weaponService;

    protected function configure()
    {
       $this
        ->setDescription('Import game.options file.')
        ->setHelp('No help yet, read src file to locate game.option file');
    }
    
    public function __construct(WeaponService $weaponService, EquipmentService $equipmentService, GearSetService $gearSetService)
    {
        $this->weaponService = $weaponService;
        $this->equipmentService = $equipmentService;
        $this->gearSetService = $gearSetService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$gameJson = file_get_contents('/var/www/sovApp/src/Command/game.json');
		$output->writeln('Filesize : '.strlen($gameJson));
		
		$gameArray = json_decode($gameJson, true);
		
		$weapons = $gameArray[0]['value']['weapons'];
		$weapon_modules = $gameArray[0]['value']['weapon_modules'];
		$gearsets = $gameArray[0]['value']['gear_sets'];
		$equipments = $gameArray[0]['value']['equipment'];
		$gameArray = null;
		
		$formatedWeapons = [];		
		foreach ($weapons as $weapon => $allstats){
			$formatedWeapons[$weapon] = $allstats['parameters'];
		}

		$formattedEquipments = [];		
			foreach ($equipments as $equipment => $equipmentStats){
				$formattedEquipments[$equipment] = $equipmentStats['parameters'];
		}	
		
		$weapons  = null;
		$equipments  = null;	
		
		$this->weaponService->makeNewWeapons($formatedWeapons);
		$this->equipmentService->makeNewEquipement($formattedEquipments);
		$this->gearSetService->makeNewGearSet($gearsets);
		$output->writeln('Nb weapons : '.count($formatedWeapons));
		$output->writeln('Nb equipments : '.count($formattedEquipments));
		$output->writeln('Nb gearSet : '.count($gearsets));
		$output->writeln('Done.');
        return 0;
    }

}
