<?php
// src/Controller/DefaultController.php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\GameVersion;
use App\Entity\Weapon;
use App\Service\WeaponService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class defaultController extends AbstractController
{

    private $em;
    private $weaponService;
    private $locale;

    public function __construct(EntityManagerInterface $em, WeaponService $weaponService)
    {
        $this->em = $em;
        $this->weaponService = $weaponService;
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->redirectToRoute('stats');
    }

    public function logVisit() {
        $count = file_get_contents($_ENV['VISITSLOG']);
        file_put_contents($_ENV['VISITSLOG'], $count + 1);
    }

    /**
     * @Route("/survarium/{param1}/{param2}", name="stats", defaults={"utm_source": false, "utm_lang": false, "param1": false, "param2": false})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function stats(Request $request, TranslatorInterface $translator)
    {
        //$this->logVisit();
        $source = $request->query->get('utm_source');
        $survariumPro = false;
        $responsive = false;

        if($source === 'svpro') {
            $survariumPro = true;
            $responsive = true;
        }

        $locale = $request->query->get('utm_lang');
        if($locale === null)  $locale = 'en';
        $this->locale = $locale;
        $osLang = $this->getHtmlLang($this->locale);
        $this->weaponService->setLocale($this->locale);

        $defaultData = $this->getDefaultSample();
        $form = $this->getTTKForm($defaultData);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $this->weaponService->setSample($form->getData());
        } else {
            $this->weaponService->setSample($defaultData);
        }

        $message = $this->weaponService->getSampleMessage();
        $weaponsArr = $this->weaponService->getWeaponsStats($survariumPro, false);
        $sampleTTKIndex = $translator->trans('sample timetokill');
        $recoilUrl = $this->generateUrl("recoilGraph", ['utm_lang' => $locale]);

        return $this->minifiedRender('stats.html.twig', [
            'weapons' => $weaponsArr,
            'message' => $message,
            'sampleTTKIndex' => $sampleTTKIndex,
            'form' => $form->createView(),
            'utm_source' => $source,
            'utm_lang' => $this->locale,
            'recoilUrl' => $recoilUrl,
            'responsive' => $responsive,
            'osLang' => $osLang,
            'survariumPro' => $survariumPro,
        ]);
    }

    protected function minifiedRender(string $view, array $parameters = [], Response $response = null): Response
    {
        $content = $this->container->get('twig')->render($view, $parameters);

        if (null === $response) {
            $response = new Response();
        }
        $content = preg_replace(array('/<!--(.*)-->/Uis',"/[[:blank:]]+/"),array('',' '),str_replace(array("\n","\r","\t"),'',$content));
        $response->setContent($content);

        return $response;
    }

    /**
     * @Route("/recoilgraph", name="recoilGraph", defaults={"weaponId": false, "utm_lang" : false})
     */
    public function recoilGraph(Request $request)
    {
        $weaponId = $request->query->get('weaponId');
        if($weaponId === null || $weaponId === false) {
            throw $this->createNotFoundException('This weapon does not exist');
        }

        $weaponRepo =  $this->em->getRepository('App:Weapon');
        /** @var Weapon $weapon */
        $weapon = $weaponRepo->find($weaponId);
        if($weapon === null) {
            throw $this->createNotFoundException('This weapon does not exist');
        }

        $locale = $request->query->get('utm_lang');
        if($locale === null) {
            $locale = 'en';
        }

        $this->locale = $locale;
        $request->setLocale($locale);
        $this->weaponService->setLocale($locale);

        $shotsParam = json_decode($weapon->getShotsParams());
        $recoilArray = [];
        $recoilArray[0] = ['y' => 0, 'x' => 0, 'label' => 'Start', 'lineColor' => "rgba(10,10,10,.8)", 'markerColor' => 'rgba(10,10,10,.8)'];
        $startX = 0;
        foreach ($shotsParam->shots_params as $index => $shotParams) {
            $A = ($shotParams[1] < 90 ) ? $shotParams[1] : 90 - ($shotParams[1] - 90);
            $b = $shotParams[0];
            $lastCoors = $recoilArray[array_key_last($recoilArray)];
            if($A == 90) {
                $a = $b;
                $c = 0;
            } else {
                $B = 90;
                $C = 180 - $B - $A;
                $a = $b * sin(deg2rad($A))/sin(deg2rad($B));
                $c = $b * sin(deg2rad($C))/sin(deg2rad($B));
                if($shotParams[1] < 90 ) {
                    $c = -$c;
                }
                $c =  $c*10;
            }
            if($startX < abs($lastCoors['x'] + $c))  {
                $startX = abs($lastCoors['x'] + $c);
            }

            //$acc = (100 - ($shotsParam->standing_stand_accuracy * $shotParams[2]));
            $shot = [
                'y' => $lastCoors['y'] + $a,
                'x' =>  $lastCoors['x'] - $c,
                'markerSize' => 6
            ];
            if($weapon->getMagazineCapacity() > 50) {
                $shot['markerSize'] = 3;
            }
            $recoilArray[] = $shot;
        }
        if($startX == 0) { // only vertical recoil
            $startX = 5;
        }

        foreach ($recoilArray as $key => $value) {
            if($key === 0) {
                continue;
            }
            $recoilArray[$key]['x'] = round($recoilArray[$key]['x'], 1);
            $recoilArray[$key]['y'] = round($recoilArray[$key]['y'], 1);
            $recoilArray[$key]['lineColor'] = 'rgba(10,10,10,.8)';
            $recoilArray[$key]['markerColor'] = 'rgba(10,10,10,.8)';
            $recoilArray[$key]['toolTipContent'] = 'shot: '. ($key) . ', posY: '. $recoilArray[$key]['y'] . ', posX: ' . $recoilArray[$key]['x'];
        };

        return $this->minifiedRender('recoilGraph.html.twig', [
            'recoilJson' => $recoilArray,
            'startX' => $startX * 1.2,
            'weaponName' => str_replace('"', "", str_replace("'", "", $weapon->translate($this->locale)->getLocalizedName()))
        ]);
    }


    public function getDefaultSample() {
        $sampleRepo = $this->em->getRepository('App:GameVersion');
        $sampleVersion = $sampleRepo->findOneBy([], ['date' => 'DESC']);
        $equipmentRepo = $this->em->getRepository('App:Equipment');
        $sampleEquipment = $equipmentRepo->findOneBy(array(
            'name' => '"Zubr UM-4" bulletproof vest',
            'gameVersion' => $sampleVersion));
        $sampleRange = "40";
        $sampleBonusArmor = true;
        $sampleBonusROF = true;
        $sampleBonusRange = true;
        $sampleOnyx = "4";
        $showSpecial = false;
        $useSilencer = true;
        return array(
            'version' => $sampleVersion,
            'bonusArmor' => $sampleBonusArmor,
            'bonusROF' => $sampleBonusROF,
            'bonusRange' => $sampleBonusRange,
            'range' => $sampleRange,
            'equipment' => $sampleEquipment,
            'onyx' => $sampleOnyx,
            'showSpecial' => $showSpecial,
            'useSilencer' => $useSilencer
        );
    }

    /**
     * @Route("/ajaxstats", name="ajaxstats", defaults={"utm_source": false, "utm_lang": false})
     */
    public function ajaxstats(Request $request)
    {
        $source = $request->query->get('utm_source');
        $survariumPro = false;

        if($source === 'svpro') {
            $survariumPro = true;
        }

        $locale = $request->query->get('utm_lang');
        if($locale === null) {
            $locale = 'en';
        }

        $this->locale = $locale;
        $request->setLocale($locale);
        $this->weaponService->setLocale($locale);

        $defaultData = $this->getDefaultSample();

        $form = $this->getTTKForm($defaultData);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $this->weaponService->setSample($form->getData());
        } else {
            $this->weaponService->setSample($defaultData);
        }

        $message = $this->weaponService->getSampleMessage();
        $weaponsArr = $this->weaponService->getWeaponsStats($survariumPro, true);
        $jsonReturn = ['message' => $message, 'data' => $weaponsArr];
        $encodings = $request->getEncodings();

        // optimize json transfer :
        // With content encoding, less data over network (about /10 for ajaxstats), probably more cpu usage
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

    public function getTTKForm($defaultData)
    {
        return $this->createFormBuilder($defaultData, array('csrf_protection' => false))
            ->add('version', EntityType::class, [
                'label' => 'Version',
                'class' => GameVersion::class,
                'choice_label' => 'name'])
            ->add('equipment', EntityType::class, [
                'label' => 'Armor',
                'class' => Equipment::class,
                'query_builder' => function (EntityRepository $er) use ( $defaultData ) {
                    return $er->createQueryBuilder('u')
                        ->select('u', 'gs', 't')
                        ->leftJoin('u.gearSet', 'gs')
                        ->leftJoin('u.translations', 't', 'WITH', 't.locale = :locale')
                        ->andWhere('gs.gameVersion = :lastVersion')
                        ->setParameter('lastVersion', $defaultData['version'])
                        ->setParameter('locale', $this->locale)
                        ->orderBy('u.gearSet', 'ASC');
                },
                'choice_label' => function (Equipment $equipment) {
                    return $equipment->translate($this->locale)->getLocalizedName();
                },
                'group_by' => 'gearSetName'
            ])
            ->add('bonusROF', CheckboxType::class, [
                'label' => '+RoF',
                'data' => true,
                'required' => false,])
            ->add('bonusRange', CheckboxType::class, [
                'label' => '+15% range',
                'data' => true,
                'required' => false,])
            ->add('onyx', RangeType::class, [
                'label' => 'Onix %',
                'attr' => ['step' => 1, 'min' => 0, 'max' => 99],
                'required' => false,])
            ->add('range', RangeType::class, [
                'label' => 'Range',
                'attr' => ['step' => 5, 'min' => 5, 'max' => 500],
                'required' => false,])
            ->add('bonusArmor', CheckboxType::class, [
                'label' => '+Armor',
                'data' => true,
                'required' => false,])
            ->add('showSpecial', CheckboxType::class, [
                'label' => 'Show special weapons',
                'required' => false,])
            ->add('useSilencer', CheckboxType::class, [
                'label' => 'Use Silencers',
                'data' => true,
                'required' => false,])
            ->add('save', SubmitType::class, ['label' => 'UPDATE'])
            ->getForm();
    }

    public function getHtmlLang($locale) {
        if($locale === 'ru') {
            return 'ru-RU';
        }

        if($locale === 'es') {
            return 'es-ES';
        }

        if($locale === 'ua') {
            return 'uk_UA';
        }

        if($locale === 'pl') {
            return 'pl-PL';
        }

        //Default english
        return 'en-US';
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