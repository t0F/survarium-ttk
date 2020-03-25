<?php
// src/Service/GameVersionService.php
namespace App\Service;

use App\Entity\GameVersion;
use Doctrine\ORM\EntityManagerInterface;


class GameVersionService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function makeNewVersion()
    {
        $version = new GameVersion();
        $version->setName('CURRENT');
        $version->setIsActive(true);
        $version->setDate(new \DateTime());

        $this->em->persist($version);
        $this->em->flush();
        return $version;
    }

    public function persistVersion(GameVersion $version)
    {
        $this->em->persist($version);
        $this->em->flush();
        return $version;
    }

    public function checkIfNewVersion($version) {
        $versionRepo = $this->em->getRepository('App:GameVersion');
        $lastImported = $versionRepo->findOneBy([], ['date' => 'DESC']);
        if($lastImported != null && $lastImported->getName() === $version) {
            return "false";
        }
        return "true";
    }

    public function getLastVersion() {
        $url = "https://survarium.com/en/news";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $curl_output = curl_exec($curl);
        curl_close($curl);
        $DOM = new \DOMDocument;
        libxml_use_internal_errors(true);
        $DOM->loadHTML( $curl_output);
        libxml_use_internal_errors(false);
        $finder  = new \DOMXpath($DOM);
        $version = 'no version found, check code ! ';

        $titles = $finder->query("//h3[contains(@class, news-title)]");
        for ($i = 0; $i < $titles->length; $i++) {
            $title = $titles->item($i)->nodeValue;
            if(strpos($title, 'is Live') !== false){
                $version = str_replace(' is Live. Play Now!', '', str_replace('Survarium Patch ', '', $title));
                break;
            }
        }
        return $version;
    }
}