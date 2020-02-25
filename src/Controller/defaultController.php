<?php
// src/Controller/DefaultController.php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\GameVersion;
use App\Service\WeaponService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
            ->add('version', EntityType::class, ['label' => 'Version ', 'class' => GameVersion::class, 'choice_label' => 'name'])
            ->add('equipment', EntityType::class, ['label' => 'Armor ', 'class' => Equipment::class, 'choice_label' => 'name'])
            ->add('onyxPass', NumberType::class, ['label' => 'OnixPass ', 'empty_data' => 0, 'scale' => 1, 'attr' => ['value' => 0, 'step' => 0.1, 'min' => 4, 'max' => 11], 'html5' => true, 'required' => false,])
            ->add('onyxAct', NumberType::class, ['label' => 'OnixAct ', 'empty_data' => 0, 'scale' => 1, 'attr' => ['value' => 0, 'step' => 0.1, 'min' => 40, 'max' => 94], 'html5' => true, 'required' => false,])
            ->add('range', NumberType::class, ['label' => 'Range', 'scale' => 1, 'attr' => ['value' => 20, 'step' => 1, 'min' => 1, 'max' => 500], 'html5' => true, 'required' => false,])
            ->add('bonusArmor', NumberType::class, ['label' => '+Armor', 'scale' => 1, 'attr' => ['value' => 5, 'step' => 1, 'min' => 0, 'max' => 5], 'html5' => true, 'required' => false,])
            ->add('save', SubmitType::class, ['label' => 'Change Sample'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $weaponService->setSample($form->getData());
        }

        $message = $this->weaponService->getSampleMessage();


        $weaponRepo = $this->getDoctrine()->getRepository('App:Weapon');
        $weaponsEnt = $weaponRepo->findAll();
        $weaponsArr = $this->weaponService->weaponsToArray($weaponsEnt);

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

        if ($weapon == null) die('Don\'t change that.');

        $weaponsTTK = $this->weaponService->weaponTTKToArray($weapon);

        $bodyParts = ['HLMT', 'MASK', 'TORS', 'HAND', 'LEGS', 'BOOT'];
        return $this->render('weaponStats.html.twig', [
            'weaponsTTK' => $weaponsTTK,
            'bodyParts' => $bodyParts,
            'weaponName' => $weapon->getFormattedName()
        ]);
    }
}