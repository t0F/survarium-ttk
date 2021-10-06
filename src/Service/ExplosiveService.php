<?php
// src/Service/GameVersionService.php
namespace App\Service;

use App\Entity\Explosive;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class ExplosiveService
{
    private $em;
    private $locale;

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        $this->translator->setLocale($this->locale);
    }

    public function deleteOldExplosives()
    {
        $explosiveRepo = $this->em->getRepository('App:Explosive');
        $explosiveRepo->deleteAll();
    }

    public function getExplosivesStats()
    {
        $explosivesRepo = $this->em->getRepository('App:Explosive');
        $explosivesEnt = $explosivesRepo->findByLocale($this->locale);
        return $this->explosivesToArray($explosivesEnt);
    }

    public function explosivesToArray($explosives)
    {
        $explosivesArray = [];
        /** @var Explosive $explosive */
        foreach ($explosives as $explosive) {
            $explosiveArray = [];
            $name = str_replace("'", "", str_replace('"', "",
                ($explosive->translate($this->locale)->getLocalizedName() !== null) ?
                    $explosive->translate($this->locale)->getLocalizedName()
                    : strtoupper(str_replace('_', ' ', $explosive->getName()))
            ));

            $explosiveArray['name'] = $name;
            $explosiveArray['maxRange'] = $explosive->getMaxRange();
            $explosiveArray['maxDamage'] = $explosive->getMaxDamage();
            $explosiveArray['minDamage'] = $explosive->getMinDamage();
            $explosivesArray[] = $explosiveArray;
        }
        return $explosivesArray;
    }

    public function importExplosives($rawExplosives, $allLocales)
    {
        $nbExplosives = 0;
        foreach ($rawExplosives as $name => $rawExplosive) {
            if (!isset($rawExplosive['data'])
                || (
                    !isset($rawExplosive['data']['explosive'])
                    && (!isset($rawExplosive['data']['mine'])
                        || !isset($rawExplosive['data']['mine']['explosive'])
                    )
                )
            ) {
                continue;
            } else {
                echo $name;
            }

            $explosiveData = isset($rawExplosive['data']['explosive'])
                ? $rawExplosive['data']['explosive']
                : $rawExplosive['data']['mine']['explosive'];
            $explosive = new Explosive();
            $explosive->setName($name);
            $explosive->setMaxDamage($explosiveData['max_damage']);
            $explosive->setMaxRange($explosiveData['max_range']);
            $explosive->setMinDamage($explosiveData['min_damage']);

            $this->translateExplosiveName($explosive, $allLocales, $rawExplosive['ui_desc']['text_descriptions']['name']);
            $this->em->persist($explosive);
            $explosive->mergeNewTranslations();
            $nbExplosives++;
        }

        $this->em->flush();
        return $nbExplosives;
    }

    public function translateExplosiveName(Explosive &$explosive, array $allLocales, string $stringToFind)
    {
        foreach ($allLocales as $localeName => $locales) {
            if (array_key_exists($stringToFind, $locales) && $locales[$stringToFind] != 'ОПИСАНИЕ НЕ ГОТОВО!!!') {
                $explosive->translate($localeName)->setLocalizedName($locales[$stringToFind]);
            }
        }

        if (array_key_exists($stringToFind, $allLocales['ba'])) {
            $explosive->setName($allLocales['ba'][$stringToFind]);
        }
    }
}