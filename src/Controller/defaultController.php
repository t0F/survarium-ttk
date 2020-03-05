<?php
// src/Controller/DefaultController.php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\GameVersion;
use App\Service\WeaponService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class defaultController extends AbstractController
{

    private $em;
    private $weaponService;

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
     * @Route("/survarium", name="stats")
     */
    public function stats(Request $request, WeaponService $weaponService)
    {
        $sampleRepo = $this->em->getRepository('App:GameVersion');
        $sampleVersion = $sampleRepo->findOneBy([], ['date' => 'DESC']);
        $equipmentRepo = $this->em->getRepository('App:Equipment');
        $sampleEquipment = $equipmentRepo->findOneBy(array(
            'name' => '"Zubr UM-4" bulletproof vest',
            'gameVersion' => $sampleVersion));
        $sampleRange = "40";
        $sampleBonusArmor = "5";
        $sampleBonusROF = "5";
        $sampleOnyx = "4";
        $defaultData = array(
            'version' => $sampleVersion,
            'bonusArmor' => $sampleBonusArmor,
            'bonusROF' => $sampleBonusROF,
            'range' => $sampleRange,
            'equipment' => $sampleEquipment,
            'onyx' => $sampleOnyx
        );

        $form = $this->getTTKForm($defaultData);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $this->weaponService->setSample($form->getData());
        } else {
            $this->weaponService->setSample($defaultData);
        }

        $message = $this->weaponService->getSampleMessage();
        $weaponsArr = $this->weaponService->getWeaponsStats();
        return $this->render('stats.html.twig', [
            'weapons' => $weaponsArr,
            'message' => $message,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ajaxstats", name="ajaxstats")
     */
    public function ajaxstats(Request $request, WeaponService $weaponService)
    {
        $sampleRepo = $this->em->getRepository('App:GameVersion');
        $sampleVersion = $sampleRepo->findOneBy([], ['date' => 'DESC']);
        $equipmentRepo = $this->em->getRepository('App:Equipment');
        $sampleEquipment = $equipmentRepo->findOneBy(array(
            'name' => 'renesanse_torso_10',
            'gameVersion' => $sampleVersion));
        $sampleRange = "40";
        $sampleBonusArmor = "5";
        $sampleBonusROF = "5";
        $sampleOnyx = "4";
        $defaultData = array(
            'version' => $sampleVersion,
            'bonusArmor' => $sampleBonusArmor,
            'bonusROF' => $sampleBonusROF,
            'range' => $sampleRange,
            'equipment' => $sampleEquipment,
            'onyx' => $sampleOnyx
        );


        $form = $this->getTTKForm($defaultData);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $this->weaponService->setSample($form->getData());
        } else {
            $this->weaponService->setSample($defaultData);
        }

        $message = $this->weaponService->getSampleMessage();
        $weaponsArr = $this->weaponService->getWeaponsStats();
        $jsonReturn = ['message' => $message, 'data' => $weaponsArr];
        $encodings = $request->getEncodings();

        //optimize json transfer :
        // With content encoding, less data over network (about /10 for this call), more cpu usage
        $response = new JsonResponse($jsonReturn);
        if (in_array('gzip', $encodings) && function_exists('gzencode')) {
            $content = gzencode($response->getContent());
            $response->setContent($content);
            $response->headers->set('Content-encoding', 'gzip');
        } elseif (in_array('deflate', $encodings) && function_exists('gzdeflate')) {
            $content = gzdeflate($response->getContent());
            $response->setContent($content);
            $response->headers->set('Content-encoding', 'deflate');
        }
        return $response;
    }

    public function getTTKForm($defaultData) {
        return $this->createFormBuilder($defaultData)
            ->add('version', EntityType::class, [
                'label' => 'Version ',
                'class' => GameVersion::class,
                'choice_label' => 'name'])
            ->add('equipment', EntityType::class, [
                'label' => 'Armor ',
                'class' => Equipment::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->select('u', 'gs')
                        ->leftJoin('u.gearSet', 'gs')
                        ->orderBy('u.gearSet', 'ASC');
                },
                'choice_label' => function (Equipment $equipment) {
                    return $equipment->getName();
                },
                'group_by' => 'gearSetName'
            ])
            ->add('bonusROF', NumberType::class, [
                'label' => '+RoF %',
                'empty_data' => 5,
                'scale' => 1,
                'attr' => ['step' => 0.1, 'min' => 0, 'max' => 5],
                'html5' => true,
                'required' => false,])
            ->add('onyx', NumberType::class, [
                'label' => 'Onix %',
                'empty_data' => 0,
                'scale' => 1,
                'attr' => ['step' => 0.1, 'min' => 0, 'max' => 99],
                'html5' => true,
                'required' => false,])
            ->add('range', NumberType::class, [
                'label' => 'Range',
                'scale' => 1,
                'attr' => ['step' => 1, 'min' => 1, 'max' => 500],
                'html5' => true,
                'required' => false,])
            ->add('bonusArmor', NumberType::class, [
                'label' => '+Armor',
                'empty_data' => 5,
                'scale' => 1,
                'attr' => ['step' => 1, 'min' => 0, 'max' => 5],
                'html5' => true,
                'required' => false,])
            ->add('save', SubmitType::class, ['label' => 'UPDATE'])
            ->getForm();
    }

    /**
     * @Route("/stats/weapon/{id}", name="weaponTTK", methods={"GET","HEAD"})
     */
    public function statsWeapon(int $id, WeaponService $weaponService)
    {
        $weaponRepo = $this->getDoctrine()->getRepository('App:Weapon');
        $weapon = $weaponRepo->findOneById($id);

        if ($weapon == null) die('Don\'t change that.');

        $weaponsTTK = $weaponService->weaponTTKToArray($weapon);

        $bodyParts = ['HLMT', 'MASK', 'TORS', 'HAND', 'LEGS', 'BOOT'];
        return $this->render('weaponStats.html.twig', [
            'weaponsTTK' => $weaponsTTK,
            'bodyParts' => $bodyParts,
            'weaponName' => $weapon->getFormattedName()
        ]);
    }
}