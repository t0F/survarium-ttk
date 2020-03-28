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
    protected static $defaultName = 'app:import';

    private $weaponService;
    private $equipmentService;
    private $gearSetService;
    private $gameVersionService;

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
        //########## GET ALL LOCALES ##########
        $localesFolders = array(
            'ch' => 'chinese',
            'en' => 'english',
            'fr' => 'french',
            'ge' => 'german',
            'or' => 'original',
            'pl' => 'polish',
            'pg' => 'portuguese',
            'ru' => 'russian',
            'es' => 'spanish',
            'tu' => 'turkish',
            'ua' => 'ukrainian',
        );
        $allLocales = array();
        $filePath = 'inputFiles/localization.json';
        if (!file_exists($filePath)) {
            $output->writeln('No file for base locale ('.$filePath.').');
            die;
        }
        $tmpFile = file_get_contents($filePath);
        $tmpCurrentLocale = json_decode($tmpFile, true);
        $allLocales['ba'] = $tmpCurrentLocale['strings'];;

        foreach ($localesFolders as $locale => $localeFolder) {
            $filePath = 'inputFiles/'.$localeFolder.'/localization.json';
            if (!file_exists($filePath)) {
                $output->writeln('No file for locale '.$localeFolder.' ('.$filePath.').');
                continue;
            }
            $tmpFile = file_get_contents($filePath);
            $tmpCurrentLocale = json_decode($tmpFile, true);
            $allLocales[$locale] = $tmpCurrentLocale['strings'];;
        }


        //########## GET GAME DATA ##########
        if (!file_exists('src/Command/game.json')) {
            $output->writeln('No file to Upload.');
            return 0;
        }

        $gameJson = file_get_contents('src/Command/game.json');
        $output->writeln('Filesize : ' . strlen($gameJson));

        $gameArray = json_decode($gameJson, true);
        $weapons = $gameArray['weapons'];
        //$weapon_modules = $gameArray[0]['value']['weapon_modules']; // not used yet
        $gearsets = $gameArray['gear_sets'];
        $equipments = $gameArray['equipment'];

        $weaponsModulelink = $gameArray['module_to_item_links'];

        $weaponsModules = $gameArray['weapon_modules'];
        $formattedWeaponsModules = [];
        foreach ($weaponsModules as $moduleName => $module) {
            $formattedWeaponsModules[$moduleName] = [
                'dictId' => $module['parameters']['dict_id'],
                'category' => $module['parameters']['category_id']
            ];
        }

        $modifications = $gameArray['modifications'];
        $formattedModifications = [];
        $modificationsForWeapons = [];

        foreach ($modifications as $modification) {
            $formattedModifications[$modification['group']] = $modification;
            $modificationsForWeapons[$modification['group'].'_'.$modification['grade']] = $modification;
        }

        $modulesInfos = $gameArray['modules_drop_options']['modules_additional_infos'];
        $modulesAndLink = [];
        foreach ($modulesInfos as $moduleInfos) {
            if (isset($moduleInfos['first_drop_modifier']) || isset($moduleInfos['second_drop_modifier'])) {
                foreach ($formattedModifications as $modification) {
                    if( $modification['grade'] == 10) {
                        foreach ($modification['modifiers'] as $modifier) {
                            if($modifier['path'][1] == 'effective_distance'
                                || $modifier['path'][1] == 'bullet_damage'
                                || $modifier['path'][1] == 'rounds_per_minute'
                                || $modifier['path'][1] == 'magazine_capacity'
                                || $modifier['path'][1] == 'bullet_damage'
                            ) {
                                foreach ($formattedWeaponsModules as $index => $weaponsModule) {
                                    if((isset($moduleInfos['first_drop_modifier'])
                                            && $modification['group'] == $moduleInfos['first_drop_modifier'])
                                        || (isset($moduleInfos['second_drop_modifier'])
                                            && $modification['group'] == $moduleInfos['second_drop_modifier'])
                                    )  {
                                        if ($formattedWeaponsModules[$index]['dictId'] === $moduleInfos['dict_id']){
                                            foreach ($weaponsModulelink as $link) {
                                                if( $link['module'] === $index) {
                                                    if(!isset($modulesAndLink[$link['item']])) {
                                                        $modulesAndLink[$link['item']] = [];
                                                    }

                                                    $currentModuleLink = [];
                                                    $currentModuleLink['moduleName'] = $link['module'];
                                                    $currentModuleLink['modifier']  = $modifier['path'][1];
                                                    $currentModuleLink['category']  = $formattedWeaponsModules[$index]['category'];

                                                    $currentModuleLink['modifierValue']  = $modifier['value'];
                                                    $modulesAndLink[$link['item']][] = $currentModuleLink;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $gameArray = null;
        //now save data for that game version in DB
        $version = $this->gameVersionService->makeNewVersion();
        $this->weaponService->makeNewWeapons($weapons, $version, $allLocales, $modificationsForWeapons, $modulesAndLink);
        $this->equipmentService->makeNewEquipement($equipments, $version, $allLocales);
        $this->gearSetService->makeNewGearSet($gearsets, $version, $allLocales);

        //########## GET ALL DONE, OUTPUT ##########
        $output->writeln('');
        $output->writeln('=======================================================');
        $output->writeln('Nb weapons : ' . count($weapons));
        $output->writeln('Nb equipments : ' . count($equipments));
        $output->writeln('Nb gearSet : ' . count($gearsets));
        $output->writeln('New version: : ' . $version->getName());
        $output->writeln('Date : ' . $version->getDate()->format('d/m/Y'));
        $output->writeln('=======================================================');
        $output->writeln('Done.');
        return 0;
    }

}
