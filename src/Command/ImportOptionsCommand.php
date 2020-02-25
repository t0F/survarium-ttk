<?php

namespace App\Command;

use App\Service\EquipmentService;
use App\Service\GameVersionService;
use App\Service\GearSetService;
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
            ->setHelp('Sorry no options, read/edit execute function to locate game.option file');
    }

    public function __construct(WeaponService $weaponService, EquipmentService $equipmentService, GearSetService $gearSetService, GameVersionService $gameVersionService)
    {
        $this->weaponService = $weaponService;
        $this->equipmentService = $equipmentService;
        $this->gearSetService = $gearSetService;
        $this->gameVersionService = $gameVersionService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$gameJson = file_get_contents('/var/www/sovApp/src/Command/game.json'); // linux
        if (!file_exists('src/Command/game.json')) {
            $output->writeln('No file to Upload.');
            return 0;
        }

        $gameJson = file_get_contents('src/Command/game.json'); // windows
        $output->writeln('Filesize : ' . strlen($gameJson));

        $gameArray = json_decode($gameJson, true);

        $weapons = $gameArray[0]['value']['weapons'];
        //$weapon_modules = $gameArray[0]['value']['weapon_modules']; // not used yet
        $gearsets = $gameArray[0]['value']['gear_sets'];
        $equipments = $gameArray[0]['value']['equipment'];
        $gameArray = null;

        $formatedWeapons = [];
        foreach ($weapons as $weapon => $allstats) {
            $formatedWeapons[$weapon] = $allstats['parameters'];
        }

        $formattedEquipments = [];
        foreach ($equipments as $equipment => $equipmentStats) {
            $formattedEquipments[$equipment] = $equipmentStats['parameters'];
        }

        $weapons = null;
        $equipments = null;

        //now save data for that game version in DB
        $version = $this->gameVersionService->makeNewVersion();
        $this->weaponService->makeNewWeapons($formatedWeapons, $version);
        $this->equipmentService->makeNewEquipement($formattedEquipments, $version);
        $this->gearSetService->makeNewGearSet($gearsets, $version);

        $output->writeln('');
        $output->writeln('=======================================================');
        $output->writeln('Nb weapons : ' . count($formatedWeapons));
        $output->writeln('Nb equipments : ' . count($formattedEquipments));
        $output->writeln('Nb gearSet : ' . count($gearsets));
        $output->writeln('New version: : "' . $version->getName());
        $output->writeln('Date : ' . $version->getDate()->format('dd/mm/yy'));
        $output->writeln('=======================================================');
        $output->writeln('Done.');
        return 0;
    }

}
