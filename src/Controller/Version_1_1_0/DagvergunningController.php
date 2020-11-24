<?php
/*
 *  Copyright (C) 2020 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace App\Controller\Version_1_1_0;

use App\Entity\Dagvergunning;
use App\Exception\FactuurServiceException;
use App\Mapper\DagvergunningMapper;
use App\Repository\DagvergunningRepository;
use App\Repository\KoopmanRepository;
use App\Service\FactuurService;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("1.1.0")
 */
class DagvergunningController extends AbstractController
{

    /**
     * Geeft een nieuwe dagvergunnning uit
     *
     * @Route("/dagvergunning_concept/", methods={"POST"})
     * @OA\Parameter(name="marktId", in="body", required="true", description="ID van de markt", @OA\Schema(type="integer"))
     * @OA\Parameter(name="dag", in="body", required="true", description="Als yyyy-mm-dd", @OA\Schema(type="string"))
     * @OA\Parameter(name="aantal3MeterKramen", in="body", required="false", description="Aantal 3 meter kramen", @OA\Schema(type="integer"))
     * @OA\Parameter(name="aantal4MeterKramen", in="body", required="false", description="Aantal 4 meter kramen", @OA\Schema(type="integer"))
     * @OA\Parameter(name="extraMeters", in="body", required="false", description="Extra meters", @OA\Schema(type="integer"))
     * @OA\Parameter(name="aantalElektra", in="body", required="false", description="Aantal elektra aansluitingen dat is afgenomen", @OA\Schema(type="integer"))
     * @OA\Parameter(name="afvaleiland", in="body", required="true", @OA\Schema(type="string"))
     * @OA\Parameter(name="eenmaligElektra", in="body", required="false", description="Eenmalige elektra kosten ongeacht plekken", @OA\Schema(type="boolean"))
     * @OA\Parameter(name="krachtstroom", in="body", required="false", description="Is er een krachtstroom aansluiting afgenomen?", @OA\Schema(type="boolean"))
     * @OA\Parameter(name="reiniging", in="body", required="false", description="Is er reiniging afgenomen?", @OA\Schema(type="boolean"))
     * @OA\Parameter(name="erkenningsnummer", in="body", required="true", description="Nummer zoals ingevoerd", @OA\Schema(type="string"))
     * @OA\Parameter(name="vervangerErkenningsnummer", in="body", required="false", description="Nummer zoals ingevoerd", @OA\Schema(type="string"))
     * @OA\Parameter(name="erkenningsnummerInvoerMethode", in="body", required="false", description="Waardes: handmatig, scan-foto, scan-nfc, scan-barcode, scan-qr, opgezocht, onbekend. Indien niet opgegeven wordt onbekend gebruikt.", @OA\Schema(type="string"))
     * @OA\Parameter(name="aanwezig", in="body", required="false", description="Aangetroffen persoon Zelf|Partner|Vervanger met toestemming|Vervanger zonder toestemming|Niet aanwezig|Niet geregisteerd", @OA\Schema(type="string"))
     * @OA\Parameter(name="notitie", in="body", required="false", description="Vrij notitie veld", @OA\Schema(type="string"))
     * @OA\Parameter(name="registratieDatumtijd", in="body", required="false", description="Datum/tijd dat de registratie is gemaakt, indien niet opgegeven wordt het moment van de request gebruikt", @OA\Schema(type="string"))
     * @OA\Parameter(name="registratieGeolocatie", in="body", required="false", description="Geolocatie waar de registratie is ingevoerd, als lat,long", @OA\Schema(type="string"))
     * @OA\Tag(name="Dagvergunning")
     * @IsGranted("ROLE_USER")
     */
    public function conceptAction(Request $request)
    {
        $message = json_decode($request->getContent(false), true);

        // check inputs
        if ($message === null) {
            return new JsonResponse(['error' => json_last_error_msg()]);
        }

        if (isset($message['marktId']) === false) {
            return new JsonResponse(['error' => 'Required field marktId is missing']);
        }

        if (isset($message['dag']) === false) {
            return new JsonResponse(['error' => 'Required field dag is missing']);
        }

        if (isset($message['erkenningsnummer']) === false) {
            return new JsonResponse(['error' => 'Required field erkenningsnummer is missing']);
        }

        if (isset($message['aanwezig']) === false) {
            return new JsonResponse(['error' => 'Required field aanwezig is missing']);
        }

        // set defaults
        if (isset($message['erkenningsnummerInvoerMethode']) === false) {
            $message['erkenningsnummerInvoerMethode'] = 'onbekend';
        }

        if (isset($message['registratieDatumtijd']) === false) {
            $message['registratieDatumtijd'] = date('Y-m-d H:i:s');
        }

        if (isset($message['registratieGeolocatie']) === false) {
            $message['registratieGeolocatie'] = null;
        }

        if (isset($message['aantal3MeterKramen']) === false) {
            $message['aantal3MeterKramen'] = 0;
        }

        if (isset($message['aantal4MeterKramen']) === false) {
            $message['aantal4MeterKramen'] = 0;
        }

        if (isset($message['extraMeters']) === false) {
            $message['extraMeters'] = 0;
        }

        if (isset($message['aantalElektra']) === false) {
            $message['aantalElektra'] = 0;
        }

        if (isset($message['afvaleiland']) === false) {
            $message['afvaleiland'] = 0;
        }

        if (isset($message['eenmaligElektra']) === false) {
            $message['eenmaligElektra'] = false;
        }

        if (isset($message['krachtstroom']) === false) {
            $message['krachtstroom'] = false;
        }

        if (isset($message['reiniging']) === false) {
            $message['reiniging'] = false;
        }

        if (isset($message['notitie']) === false) {
            $message['notitie'] = '';
        }

        if (isset($message['vervangerErkenningsnummer']) === false) {
            $message['vervangerErkenningsnummer'] = null;
        }

        $factuurService = $this->get('appapi.factuurservice');

        try {
            $dagvergunning = $factuurService->createDagvergunning(
                $message['marktId'],
                $message['dag'],
                $message['erkenningsnummer'],
                $message['aanwezig'],
                $message['erkenningsnummerInvoerMethode'],
                $message['registratieDatumtijd'],
                $message['registratieGeolocatie'],
                $message['aantal3MeterKramen'],
                $message['aantal4MeterKramen'],
                $message['extraMeters'],
                $message['aantalElektra'],
                $message['afvaleiland'],
                $message['eenmaligElektra'],
                $message['krachtstroom'],
                $message['reiniging'],
                $message['notitie'],
                $this->getUser(),
                $message['vervangerErkenningsnummer']
            );
        } catch (FactuurServiceException $e) {
            return new JsonResponse(['error' => $e->getMessage()]);
        }

        /* @var $mapper \App\Mapper\FactuurMapper */
        $mapper = $this->get('appapi.mapper.factuur');

        $factuur = $factuurService->createFactuur($dagvergunning);
        $response = $mapper->singleEntityToModel($factuur);

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Geeft een nieuwe dagvergunnning uit
     *
     * @Route("/dagvergunning/", methods={"POST"})
     * @OA\Parameter(name="marktId", in="body", required="true", description="ID van de markt", @OA\Schema(type="integer"))
     * @OA\Parameter(name="dag", in="body", required="true", description="Als yyyy-mm-dd", @OA\Schema(type="string"))
     * @OA\Parameter(name="aantal3MeterKramen", in="body", required="false", description="Aantal 3 meter kramen", @OA\Schema(type="integer"))
     * @OA\Parameter(name="aantal4MeterKramen", in="body", required="false", description="Aantal 4 meter kramen", @OA\Schema(type="integer"))
     * @OA\Parameter(name="extraMeters", in="body", required="false", description="Extra meters", @OA\Schema(type="integer"))
     * @OA\Parameter(name="aantalElektra", in="body", required="false", description="Aantal elektra aansluitingen dat is afgenomen", @OA\Schema(type="integer"))
     * @OA\Parameter(name="afvaleiland", required="true", @OA\Schema(type="string"))
     * @OA\Parameter(name="eenmaligElektra", in="body", required="false", description="Eenmalige elektra kosten ongeacht plekken", @OA\Schema(type="boolean"))
     * @OA\Parameter(name="krachtstroom", in="body", required="false", description="Is er een krachtstroom aansluiting afgenomen?", @OA\Schema(type="boolean"))
     * @OA\Parameter(name="reiniging", in="body", required="false", description="Is er reiniging afgenomen?", @OA\Schema(type="boolean"))
     * @OA\Parameter(name="erkenningsnummer", in="body", required="true", description="Nummer zoals ingevoerd", @OA\Schema(type="string"))
     * @OA\Parameter(name="vervangerErkenningsnummer", in="body", required="false", description="Nummer zoals ingevoerd", @OA\Schema(type="string"))
     * @OA\Parameter(name="erkenningsnummerInvoerMethode", in="body", required="false", description="Waardes: handmatig, scan-foto, scan-nfc, scan-barcode, scan-qr, opgezocht, onbekend. Indien niet opgegeven wordt onbekend gebruikt.", @OA\Schema(type="string"))
     * @OA\Parameter(name="aanwezig", in="body", required="false", description="Aangetroffen persoon Zelf|Partner|Vervanger met toestemming|Vervanger zonder toestemming|Niet aanwezig|Niet geregisteerd", @OA\Schema(type="string"))
     * @OA\Parameter(name="notitie", in="body", required="false", description="Vrij notitie veld", @OA\Schema(type="string"))
     * @OA\Parameter(name="registratieDatumtijd", in="body", required="false", description="Datum/tijd dat de registratie is gemaakt, indien niet opgegeven wordt het moment van de request gebruikt", @OA\Schema(type="string"))
     * @OA\Parameter(name="registratieGeolocatie", in="body", required="false", description="Geolocatie waar de registratie is ingevoerd, als lat,long", @OA\Schema(type="string"))
     * @OA\Tag(name="Dagvergunning")
     * @IsGranted("ROLE_USER")
     */
    public function createAction(EntityManagerInterface $em, Request $request)
    {
        $message = json_decode($request->getContent(false), true);

        // check inputs
        if ($message === null) {
            return new JsonResponse(['error' => json_last_error_msg()]);
        }

        if (isset($message['marktId']) === false) {
            return new JsonResponse(['error' => 'Required field marktId is missing']);
        }

        if (isset($message['dag']) === false) {
            return new JsonResponse(['error' => 'Required field dag is missing']);
        }

        if (isset($message['erkenningsnummer']) === false) {
            return new JsonResponse(['error' => 'Required field erkenningsnummer is missing']);
        }

        if (isset($message['aanwezig']) === false) {
            return new JsonResponse(['error' => 'Required field aanwezig is missing']);
        }

        // set defaults
        if (isset($message['erkenningsnummerInvoerMethode']) === false) {
            $message['erkenningsnummerInvoerMethode'] = 'onbekend';
        }

        if (isset($message['registratieDatumtijd']) === false) {
            $message['registratieDatumtijd'] = date('Y-m-d H:i:s');
        }

        if (isset($message['registratieGeolocatie']) === false) {
            $message['registratieGeolocatie'] = null;
        }

        if (isset($message['aantal3MeterKramen']) === false) {
            $message['aantal3MeterKramen'] = 0;
        }

        if (isset($message['aantal4MeterKramen']) === false) {
            $message['aantal4MeterKramen'] = 0;
        }

        if (isset($message['extraMeters']) === false) {
            $message['extraMeters'] = 0;
        }

        if (isset($message['aantalElektra']) === false) {
            $message['aantalElektra'] = 0;
        }

        if (isset($message['afvaleiland']) === false) {
            $message['afvaleiland'] = 0;
        }

        if (isset($message['eenmaligElektra']) === false) {
            $message['eenmaligElektra'] = false;
        }

        if (isset($message['krachtstroom']) === false) {
            $message['krachtstroom'] = false;
        }

        if (isset($message['reiniging']) === false) {
            $message['reiniging'] = false;
        }

        if (isset($message['notitie']) === false) {
            $message['notitie'] = '';
        }

        if (isset($message['vervangerErkenningsnummer']) === false) {
            $message['vervangerErkenningsnummer'] = null;
        }

        $factuurService = $this->get('appapi.factuurservice');

        try {
            $dagvergunning = $factuurService->createDagvergunning(
                $message['marktId'],
                $message['dag'],
                $message['erkenningsnummer'],
                $message['aanwezig'],
                $message['erkenningsnummerInvoerMethode'],
                $message['registratieDatumtijd'],
                $message['registratieGeolocatie'],
                $message['aantal3MeterKramen'],
                $message['aantal4MeterKramen'],
                $message['extraMeters'],
                $message['aantalElektra'],
                $message['afvaleiland'],
                $message['eenmaligElektra'],
                $message['krachtstroom'],
                $message['reiniging'],
                $message['notitie'],
                $this->getUser(),
                $message['vervangerErkenningsnummer']
            );
        } catch (FactuurServiceException $e) {
            return new JsonResponse(['error' => $e->getMessage()]);
        }

        $em->persist($dagvergunning);
        $em->flush();

        $factuurService = $this->get('appapi.factuurservice');
        $factuur = $factuurService->createFactuur($dagvergunning);
        $factuurService->saveFactuur($factuur);

        $result = $this->get('appapi.mapper.dagvergunning')->singleEntityToModel($dagvergunning);

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Geeft dagvergunningen
     *
     * @Route("/dagvergunning/", methods={"GET"})
     * @OA\Parameter(name="marktId", in="query", required="false", description="ID van de markt", @OA\Schema(type="integer"))
     * @OA\Parameter(name="dag", in="query", required="false", description="Als yyyy-mm-dd", @OA\Schema(type="string"))
     * @OA\Parameter(name="dagStart", in="query", required="false", description="Als yyyy-mm-dd, alleen i.c.m. dagEind", @OA\Schema(type="string"))
     * @OA\Parameter(name="dagEind", in="query", required="false", description="Als yyyy-mm-dd, alleen i.c.m. dagStart", @OA\Schema(type="string"))
     * @OA\Parameter(name="koopmanId", in="query", required="false", description="Id van de koopman", @OA\Schema(type="integer"))
     * @OA\Parameter(name="erkenningsnummer", in="query", required="false", description="Nummer van koopman waarop vergunning is uitgeschreven", @OA\Schema(type="integer"))
     * @OA\Parameter(name="doorgehaald", in="query", required="false", description="Indien niet opgenomen of leeg of 0 enkel niet doorgehaalde dagvergunningen, indien opgenomen en 1 dan enkel doorgehaalde dagvergunningen", @OA\Schema(type="integer"))
     * @OA\Parameter(name="accountId", in="query", required="false", description="Filter op de persoon die de dagvergunning uitgegeven heeft", @OA\Schema(type="integer"))
     * @OA\Parameter(name="listOffset", in="query", required="false", description="", @OA\Schema(type="integer"))
     * @OA\Parameter(name="listLength", in="query", required="false", description="Default=100", @OA\Schema(type="integer"))
     * @OA\Tag(name="Dagvergunning")
     * @IsGranted("ROLE_USER")
     */
    public function listAction(DagvergunningRepository $dagvergunningRepository, DagvergunningMapper $dagvergunningMapper, Request $request)
    {
        $q = [];
        if ($request->query->has('marktId') === true) {
            $q['marktId'] = $request->query->get('marktId');
        }

        if ($request->query->has('dag') === true) {
            $q['dag'] = $request->query->get('dag');
        }

        if ($request->query->has('dagStart') === true && $request->query->has('dagEind') === true) {
            $q['dagRange'] = [$request->query->get('dagStart'), $request->query->get('dagEind')];
        }

        if ($request->query->has('koopmanId') === true) {
            $q['koopmanId'] = $request->query->get('koopmanId');
        }

        if ($request->query->has('erkenningsnummer') === true) {
            $q['erkenningsnummer'] = str_replace('.', '', $request->query->get('erkenningsnummer'));
        }

        if ($request->query->has('doorgehaald') === true) {
            $q['doorgehaald'] = $request->query->get('doorgehaald');
        }

        if ($request->query->has('accountId') === true) {
            $q['accountId'] = $request->query->get('accountId');
        }

        $results = $dagvergunningRepository->search($q, $request->query->get('listOffset'), $request->query->get('listLength', 300));

        $response = $dagvergunningMapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Geeft dagvergunningen terug per koopman en datum
     *
     * @Route("/dagvergunning_by_date/{koopmanId}/{startDate}/{endDate}", methods={"GET"})
     * @OA\Parameter(name="koopmanId", in="path", required="true", description="Koopman id", @OA\Schema(type="integer"))
     * @OA\Parameter(name="startDate", in="path", required="true", description="Als yyyy-mm-dd", @OA\Schema(type="string"))
     * @OA\Parameter(name="endDate", in="path", required="true", description="Als yyyy-mm-dd", @OA\Schema(type="string"))
     * @OA\Tag(name="Dagvergunning")
     * @IsGranted("ROLE_SENIOR")
     */
    public function listByDateAction(
        DagvergunningRepository $dagvergunningRepository,
        DagvergunningMapper $dagvergunningMapper,
        KoopmanRepository $koopmanRepository,
        $koopmanId,
        $startDate,
        $endDate) {
        $koopman = $koopmanRepository->findOneById($koopmanId);

        if (null === $koopman) {
            throw $this->createNotFoundException('Can\'t find koopman with id ' . $koopmanId);
        }

        $sDate = new \DateTime($startDate . ' 00:00');
        $eDate = new \DateTime($endDate . ' 00:00');

        $results = $dagvergunningRepository->getByKoopmanAndDates($koopman, $sDate, $eDate);

        $response = $dagvergunningMapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Voert een doorhaling van de dagvergunning uit
     *
     * @Route("/dagvergunning/{id}", methods={"DELETE"})
     * @OA\Parameter(name="id", in="path", required="true", description="Dagvergunning id", @OA\Schema(type="integer"))
     * @OA\Parameter(name="doorgehaaldDatumtijd", in="body", required="false", description="Datum/tijd dat de doorhaling is uitgevoerd, indien niet opgegeven wordt het moment van de request gebruikt", @OA\Schema(type="string"))
     * @OA\Parameter(name="doorgehaaldGeolocatie", in="body", required="false", description="Geolocatie waar de doorhaling is uitgevoerd, als lat,long", @OA\Schema(type="string"))
     * @OA\Tag(name="Dagvergunning")
     * @IsGranted("ROLE_USER")
     */
    public function deleteAction(EntityManagerInterface $em, Request $request, $id)
    {
        /* @var $repoDagvergunning \App\Entity\DagvergunningRepository */
        $repoDagvergunning = $this->get('appapi.repository.dagvergunning');

        // read message body
        $message = json_decode($request->getContent(false), true);

        // set message defaults
        if (isset($message['doorgehaaldDatumtijd']) === false) {
            $message['doorgehaaldDatumtijd'] = date('Y-m-d H:i:s');
        }

        if (isset($message['doorgehaaldGeolocatie']) === false) {
            $message['doorgehaaldGeolocatie'] = null;
        }

        // get object
        $dagvergunning = $repoDagvergunning->getById($id);
        if ($dagvergunning === null) {
            throw $this->createNotFoundException('Can not find dagvergunning with id ' . $id);
        }

        if ($dagvergunning->isDoorgehaald() === true) {
            throw $this->createNotFoundException('Dagvergunning with id ' . $id . ' already doorgehaald');
        }

        // modify object
        $dagvergunning->setDoorgehaald(true);
        $dagvergunning->setDoorgehaaldDatumtijd(\DateTime::createFromFormat('Y-m-d H:i:s', $message['doorgehaaldDatumtijd']));
        if ($message['doorgehaaldGeolocatie'] !== null && $message['doorgehaaldGeolocatie'] !== '') {
            $point = explode(',', $message['doorgehaaldGeolocatie']);
            $dagvergunning->setDoorgehaaldGeolocatie($point[0], $point[1]);
        }

        // set account
        $dagvergunning->setDoorgehaaldAccount($this->getUser());

        // save object
        $em->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Geeft een nieuwe dagvergunnning uit
     *
     * @Route("/dagvergunning/{id}", methods={"PUT"})
     * @OA\Parameter(name="marktId", required="true", description="ID van de markt", @OA\Schema(type="integer"))
     * @OA\Parameter(name="dag", required="true", description="Als yyyy-mm-dd", @OA\Schema(type="string"))
     * @OA\Parameter(name="aantal3MeterKramen", required="false", description="Aantal 3 meter kramen", @OA\Schema(type="integer"))
     * @OA\Parameter(name="aantal4MeterKramen", required="false", description="Aantal 4 meter kramen", @OA\Schema(type="integer"))
     * @OA\Parameter(name="extraMeters", required="false", description="Extra meters", @OA\Schema(type="integer"))
     * @OA\Parameter(name="aantalElektra", required="false", description="Aantal elektra aansluitingen dat is afgenomen", @OA\Schema(type="integer"))
     * @OA\Parameter(name="afvaleiland", required="true", @OA\Schema(type="string"))
     * @OA\Parameter(name="eenmaligElektra", required="false", description="Eenmalige elektra kosten ongeacht plekken", @OA\Schema(type="boolean"))
     * @OA\Parameter(name="krachtstroom", required="false", description="Is er een krachtstroom aansluiting afgenomen?", @OA\Schema(type="boolean"))
     * @OA\Parameter(name="reiniging", required="false", description="Is er reiniging afgenomen?", @OA\Schema(type="boolean"))
     * @OA\Parameter(name="erkenningsnummer", required="true", description="Nummer zoals ingevoerd", @OA\Schema(type="string"))
     * @OA\Parameter(name="vervangerErkenningsnummer", required="false", description="Nummer zoals ingevoerd", @OA\Schema(type="string"))
     * @OA\Parameter(name="erkenningsnummerInvoerMethode", required="false", description="Waardes: handmatig, scan-foto, scan-nfc, scan-barcode, scan-qr, opgezocht, onbekend. Indien niet opgegeven wordt onbekend gebruikt.", @OA\Schema(type="string"))
     * @OA\Parameter(name="aanwezig", required="false", description="Aangetroffen persoon Zelf|Partner|Vervanger met toestemming|Vervanger zonder toestemming|Niet aanwezig|Niet geregisteerd", @OA\Schema(type="string"))
     * @OA\Parameter(name="notitie", required="false", description="Vrij notitie veld", @OA\Schema(type="string"))
     * @OA\Parameter(name="registratieDatumtijd", required="false", description="Datum/tijd dat de registratie is gemaakt, indien niet opgegeven wordt het moment van de request gebruikt", @OA\Schema(type="string"))
     * @OA\Parameter(name="registratieGeolocatie", required="false", description="Geolocatie waar de registratie is ingevoerd, als lat,long", @OA\Schema(type="string"))
     * @OA\Tag(name="Dagvergunning")
     * @IsGranted("ROLE_USER")
     */
    public function updateAction(EntityManagerInterface $em, FactuurService $factuurService, DagvergunningMapper $mapper, Request $request, $id)
    {
        $request->query->set('doorgehaaldDatumtijd', $request->query->get('registratieDatumtijd'));
        $request->query->set('doorgehaaldGeolocatie', $request->query->get('registratieGeolocatie'));

        $this->deleteAction($em, $request, $id);

        //////

        $message = json_decode($request->getContent(false), true);

        // check inputs
        if ($message === null) {
            return new JsonResponse(['error' => json_last_error_msg()]);
        }

        if (isset($message['marktId']) === false) {
            return new JsonResponse(['error' => 'Required field marktId is missing']);
        }

        if (isset($message['dag']) === false) {
            return new JsonResponse(['error' => 'Required field dag is missing']);
        }

        if (isset($message['erkenningsnummer']) === false) {
            return new JsonResponse(['error' => 'Required field erkenningsnummer is missing']);
        }

        if (isset($message['aanwezig']) === false) {
            return new JsonResponse(['error' => 'Required field aanwezig is missing']);
        }

        // set defaults
        if (isset($message['erkenningsnummerInvoerMethode']) === false) {
            $message['erkenningsnummerInvoerMethode'] = 'onbekend';
        }

        if (isset($message['registratieDatumtijd']) === false) {
            $message['registratieDatumtijd'] = date('Y-m-d H:i:s');
        }

        if (isset($message['registratieGeolocatie']) === false) {
            $message['registratieGeolocatie'] = null;
        }

        if (isset($message['aantal3MeterKramen']) === false) {
            $message['aantal3MeterKramen'] = 0;
        }

        if (isset($message['aantal4MeterKramen']) === false) {
            $message['aantal4MeterKramen'] = 0;
        }

        if (isset($message['extraMeters']) === false) {
            $message['extraMeters'] = 0;
        }

        if (isset($message['aantalElektra']) === false) {
            $message['aantalElektra'] = 0;
        }

        if (isset($message['afvaleiland']) === false) {
            $message['afvaleiland'] = 0;
        }

        if (isset($message['eenmaligElektra']) === false) {
            $message['eenmaligElektra'] = false;
        }

        if (isset($message['krachtstroom']) === false) {
            $message['krachtstroom'] = false;
        }

        if (isset($message['reiniging']) === false) {
            $message['reiniging'] = false;
        }

        if (isset($message['notitie']) === false) {
            $message['notitie'] = '';
        }

        if (isset($message['vervangerErkenningsnummer']) === false) {
            $message['vervangerErkenningsnummer'] = null;
        }

        try {
            $dagvergunning = $factuurService->createDagvergunning(
                $message['marktId'],
                $message['dag'],
                $message['erkenningsnummer'],
                $message['aanwezig'],
                $message['erkenningsnummerInvoerMethode'],
                $message['registratieDatumtijd'],
                $message['registratieGeolocatie'],
                $message['aantal3MeterKramen'],
                $message['aantal4MeterKramen'],
                $message['extraMeters'],
                $message['aantalElektra'],
                $message['afvaleiland'],
                $message['eenmaligElektra'],
                $message['krachtstroom'],
                $message['reiniging'],
                $message['notitie'],
                $this->getUser(),
                $message['vervangerErkenningsnummer']
            );
        } catch (FactuurServiceException $e) {
            return new JsonResponse(['error' => $e->getMessage()]);
        }

        $em->persist($dagvergunning);
        $em->flush();

        $factuur = $factuurService->createFactuur($dagvergunning);
        $factuurService->saveFactuur($factuur);

        $result = $mapper->singleEntityToModel($dagvergunning);

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Haal de details van een dagvergunning op
     *
     * @Route("/dagvergunning/{id}", methods={"GET"})
     * @OA\Parameter(name="id", in="path", required="true", description="Dagvergunning id", @OA\Schema(type="integer"))
     * @OA\Tag(name="Dagvergunning")
     * @IsGranted("ROLE_USER")
     */
    public function detailAction(DagvergunningRepository $repository, DagvergunningMapper $mapper, $id)
    {
        $dagvergunning = $repository->getById($id);

        $result = $mapper->singleEntityToModel($dagvergunning);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
