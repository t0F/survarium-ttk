<?php
// src/Controller/DefaultController.php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\WeaponService;
 	
class defaultController extends AbstractController
{
	
	 private $em;

    public function __construct(EntityManagerInterface $em, WeaponService $weaponService)
    {
        $this->em = $em;
        $this->weaponService = $weaponService;
    }
    
	 /**
     * @Route("/", name="index")
     */
    public function index(WeaponService $weaponService)
    {
			return $this->redirectToRoute('stats');
    }    
    
	 /**
     * @Route("/stats", name="stats")
     */
    public function stats(WeaponService $weaponService)
    {
    	  $weaponRepo = $this->getDoctrine()->getRepository('App:Weapon');
    	  $weaponsEnt = $weaponRepo->findAll();
    	  $weaponsArr = $this->weaponService->weaponsToArray( $weaponsEnt );

    	  $equipmentRepo = $this->getDoctrine()->getRepository('App:Equipment');
    	  $equipmentsEnt = $equipmentRepo->findOneByDictId(1269);
			//dump($equipmentsEnt);die;
        return $this->render('stats.html.twig', [
            'weapons' => $weaponsArr,
        ]);
    }
}