<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PulseTTKCommand extends Command
{
    protected static $defaultName = 'app:pulseTTK';
    private $gameVersionService;

    protected function configure()
    {
        $this
            ->setDescription('Import last game version from survarim.pro.')
            ->setHelp('no option');
    }

    public function __construct()
    {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = "https://pi4.freeboxos.fr/survarium";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $curl_output = curl_exec($curl);
        curl_close($curl);
        $date = \DateTime::ATOM;
        $output->writeln("Warming ".$url." cache ( ".$date." ).");
        return 0;
    }
}
