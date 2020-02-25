<?php
// src/Controller/DefaultController.php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\WeaponService;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Weapon;
use App\Entity\Equipment;
use App\Entity\GearSet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
    public function stats(Request $request, WeaponService $weaponService)
    {
    	
			$form = $this->createFormBuilder()
				->add('equipment', EntityType::class, ['label' => 'Armor WIP : ', 'class' => Equipment::class, 'choice_label' => 'name']) // 'formattedName'])
            ->add('onyx', NumberType::class, ['label' => 'Onix armorWIP : ', 'empty_data'=> 0, 'scale'=> 1, 'attr' =>  ['class' => 'form-inline'], 'attr' => ['value' =>0, 'step'=> 0.1, 'min' => 0, 'max' => 8], 'html5'=> true, 'required' => false,])
            ->add('range', NumberType::class, ['label' => 'RangeWIP : ', 'attr' => ['class' => 'form-inline'], 'scale'=> 1, 'attr' => ['value' => 20, 'step'=> 1, 'min' => 1, 'max' => 500], 'html5'=> true, 'required' => false,])
				->add('bonusArmor', NumberType::class, ['label' => 'Armor modifierWIP : ', 'attr' => ['class' => 'form-inline'], 'scale'=> 1, 'attr' => ['value' => 5, 'step'=> 1, 'min' => 0, 'max' => 5], 'html5'=> true, 'required' => false,])            
            ->add('save', SubmitType::class, [ 'label' => 'Change Sample', 'attr' => ['class' => 'form-inline btn btn-secondary btn-sm']])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {     
            $weaponService->setSample($form->getData()); 
        }
        
    		$message = $this->weaponService->getSampleMessage();
			    		
    		
    	  $weaponRepo = $this->getDoctrine()->getRepository('App:Weapon');
    	  $weaponsEnt = $weaponRepo->findAll();
    	  $weaponsArr = $this->weaponService->weaponsToArray( $weaponsEnt );

        return $this->render('stats.html.twig', [
            'weapons' => $weaponsArr,
            'message' => $message,
            'form' => $form->createView()
        ]);
    }
    
     /**
     * @Route("/stats/weapon/{id}", name="weaponTTK", methods={"GET","HEAD"})
     */
    public function statsWeapon(int $id, WeaponService $weaponService)
    {
    	  $weaponRepo = $this->getDoctrine()->getRepository('App:Weapon');
    	  $weapon = $weaponRepo->findOneById($id);

    	  if($weapon == null) die('Don\'t change that.');
    	  
    	  $weaponsTTK = $this->weaponService->weaponTTKToArray( $weapon );
    	  
    	  $bodyParts = ['HLMT','MASK','TORS','HAND','LEGS','BOOT'];
        return $this->render('weaponStats.html.twig', [
            'weaponsTTK' => $weaponsTTK,
            'bodyParts' => $bodyParts, 
            'weaponName' => $weapon->getFormattedName()
        ]);
    }
}