<?php
// src/Service/GameVersionService.php
namespace App\Service;

use App\Entity\GameVersion;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Version;


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
        $version->setName('Where do I find version in .options ? ');
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
}