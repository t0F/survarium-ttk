<?php

namespace App\Command;

use App\Service\WeaponService;
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
    
    public function __construct(WeaponService $weaponService)
    {
        $this->weaponService = $weaponService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$gameJson = file_get_contents('/html/sovApp/src/Command/game.json');
		$output->writeln('size : '.strlen($gameJson));
		
		$gameArray = json_decode($gameJson, true);
		
		$weapons = $gameArray[0]['value']['weapons'];
		$weapon_modules = $gameArray[0]['value']['weapon_modules'];
		$gameArray = null;
		
		$formatedWeapons = [];		
		foreach ($weapons as $weapon => $allstats){
			$formatedWeapons[$weapon] = $allstats['parameters'];
		}
		$weapons  = null;	
		$this->weaponService->makeNewWeapons($formatedWeapons);
		$output->writeln('nb weapons : '.count($formatedWeapons));
		$output->writeln('done.');
        return 0;
    }

}
