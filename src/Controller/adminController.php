<?php
// src/Controller/adminController.php

namespace App\Controller;

use App\Entity\WeaponConfiguration;
use App\Service\WeaponService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Dotenv\Dotenv;

class adminController extends AbstractController
{

    private $em;
    private $weaponService;
    private $locale;
    private $hash;

    public function __construct(EntityManagerInterface $em, WeaponService $weaponService)
    {
        $this->em = $em;
        $this->weaponService = $weaponService;

        $dotEnv = new Dotenv();
        $dotEnv->load(__DIR__.'/../../.env');
        $this->hash = $_ENV['hash'];
    }

    /**
     * @Route("/admin", name="adminIndex", defaults={"hash": false})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function index(Request $request, TranslatorInterface $translator)
    {
        $hash = $request->query->get('hash');
        if($hash === $this->hash) {
            $wcs = $this->em->getRepository('App:WeaponConfiguration')->findAll();
            $updateUrl = $this->generateUrl('updateWC', array('hash' => $hash));
            return $this->render('admin.html.twig', [
                'wcs' => $wcs,
                'updateUrl' => $updateUrl
            ]);
        } else {
            throw new AccessDeniedException();
        }
    }

    /**
     * @Route("/adminupdate", name="updateWC", defaults={"hash": false})
     * @param Request $request
     * @return Response
     */
    public function updateWC(Request $request)
    {
        $hash = $request->query->get('hash');
        if($hash === $this->hash) {
            $allWc = $this->em->getRepository('App:WeaponConfiguration')->findAll();
            /** @var WeaponConfiguration $currentWc */
            foreach ($allWc as $currentWc) {
                $currentWc->setIsSpecial(false);
                $this->em->persist($currentWc);
            }
            $this->em->flush();

            $wcs = $request->request->get('wcMultiSelect');
            foreach ($wcs as $currentWc) {
                /** @var WeaponConfiguration $wc */
                $wc = $allWc = $this->em->getRepository('App:WeaponConfiguration')->find($currentWc);
                $wc->setIsSpecial(true);
            }

            //Check if need to flag as special weapon (events, premium, etc)
            $weaponRepo = $this->em->getRepository('App:Weapon');
            $weaponConfRepo = $this->em->getRepository('App:WeaponConfiguration');
            $weapons = $weaponRepo->findAll();
            foreach ($weapons as $weapon) {
                $weaponConf = $weaponConfRepo->findOneByName($weapon->getName());
                if($weaponConf !== null) {
                    /** @var WeaponConfiguration $weaponConf */
                    $weapon->setIsSpecial($weaponConf->getIsSpecial());
                    $this->em->persist($weapon);
                }
            }
            $this->em->flush();

            return $this->redirectToRoute('adminIndex', array('hash' => $hash));
        } else {
            throw new AccessDeniedException();
        }
    }
}