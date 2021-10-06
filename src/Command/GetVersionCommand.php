<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\GameVersionService;

class GetVersionCommand extends Command
{
    protected static $defaultName = 'app:getversion';
    private $gameVersionService;

    protected function configure()
    {
        $this
            ->setDescription('Import last game version from survarim.pro.')
            ->setHelp('no option');
    }

    public function __construct(GameVersionService $gameVersionService)
    {
        $this->gameVersionService = $gameVersionService;
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lastVersion = $this->gameVersionService->getLastVersion();
        //$output->writeln($lastVersion); // uncomment only for dev
        $output->write($this->gameVersionService->checkIfNewVersion($lastVersion));
        return 0;
    }
}
