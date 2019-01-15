<?php
/*
 *  Copyright (C) 2017 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Controller\Version_1_1_0;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Exception\FactuurServiceException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("1.1.0")
 */
class DagvergunningController extends Controller
{

    /**
     * Geeft een nieuwe dagvergunnning uit
     *
     * @Method("POST")
     * @Route("/dagvergunning_concept/")
     * @ApiDoc(
     *  section="Dagvergunning",
     *  parameters={
     *      {"name"="marktId", "dataType"="integer", "required"=true, "description"="ID van de markt"},
     *      {"name"="dag", "dataType"="string", "required"=true, "description"="Als yyyy-mm-dd"},
     *      {"name"="aantal3MeterKramen", "dataType"="integer", "required"=false, "description"="Aantal 3 meter kramen"},
     *      {"name"="aantal4MeterKramen", "dataType"="integer", "required"=false, "description"="Aantal 4 meter kramen"},
     *      {"name"="extraMeters", "dataType"="integer", "required"=false, "description"="Extra meters"},
     *      {"name"="aantalElektra", "dataType"="integer", "required"=false, "description"="Aantal elektra aansluitingen dat is afgenomen"},
     *      {"name"="afvaleiland", "dataType"="string", "required"="true"},
     *      {"name"="eenmaligElektra", "dataType"="boolean", "required"=false, "description"="Eenmalige elektra kosten ongeacht plekken"},
     *      {"name"="krachtstroom", "dataType"="boolean", "required"=false, "description"="Is er een krachtstroom aansluiting afgenomen?"},
     *      {"name"="reiniging", "dataType"="boolean", "required"=false, "description"="Is er reiniging afgenomen?"},
     *      {"name"="erkenningsnummer", "dataType"="string", "required"=true, "description"="Nummer zoals ingevoerd"},
     *      {"name"="vervangerErkenningsnummer", "dataType"="string", "required"=false, "description"="Nummer zoals ingevoerd"},
     *      {"name"="erkenningsnummerInvoerMethode", "dataType"="string", "required"=false, "description"="Waardes: handmatig, scan-foto, scan-nfc, scan-barcode, scan-qr, opgezocht, onbekend. Indien niet opgegeven wordt onbekend gebruikt."},
     *      {"name"="aanwezig", "dataType"="string", "required"=false, "description"="Aangetroffen persoon Zelf|Partner|Vervanger met toestemming|Vervanger zonder toestemming|Niet aanwezig|Niet geregisteerd"},
     *      {"name"="notitie", "dataType"="string", "required"=false, "description"="Vrij notitie veld"},
     *      {"name"="registratieDatumtijd", "dataType"="string", "required"=false, "description"="Datum/tijd dat de registratie is gemaakt, indien niet opgegeven wordt het moment van de request gebruikt"},
     *      {"name"="registratieGeolocatie", "dataType"="string", "required"=false, "description"="Geolocatie waar de registratie is ingevoerd, als lat,long"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function conceptAction(Request $request)
    {
        $message = json_decode($request->getContent(false), true);

        // check inputs
        if ($message === null)
            return new JsonResponse(['error' => json_last_error_msg()]);
        if (isset($message['marktId']) === false)
            return new JsonResponse(['error' => 'Required field marktId is missing']);
        if (isset($message['dag']) === false)
            return new JsonResponse(['error' => 'Required field dag is missing']);
        if (isset($message['erkenningsnummer']) === false)
            return new JsonResponse(['error' => 'Required field erkenningsnummer is missing']);
        if (isset($message['aanwezig']) === false)
            return new JsonResponse(['error' => 'Required field aanwezig is missing']);

        // set defaults
        if (isset($message['erkenningsnummerInvoerMethode']) === false)
            $message['erkenningsnummerInvoerMethode'] = 'onbekend';
        if (isset($message['registratieDatumtijd']) === false)
            $message['registratieDatumtijd'] = date('Y-m-d H:i:s');
        if (isset($message['registratieGeolocatie']) === false)
            $message['registratieGeolocatie'] = null;
        if (isset($message['aantal3MeterKramen']) === false)
            $message['aantal3MeterKramen'] = 0;
        if (isset($message['aantal4MeterKramen']) === false)
            $message['aantal4MeterKramen'] = 0;
        if (isset($message['extraMeters']) === false)
            $message['extraMeters'] = 0;
        if (isset($message['aantalElektra']) === false)
            $message['aantalElektra'] = 0;
        if (isset($message['afvaleiland']) === false)
            $message['afvaleiland'] = 0;
        if (isset($message['eenmaligElektra']) === false)
            $message['eenmaligElektra'] = false;
        if (isset($message['krachtstroom']) === false)
            $message['krachtstroom'] = false;
        if (isset($message['reiniging']) === false)
            $message['reiniging'] = false;
        if (isset($message['notitie']) === false)
            $message['notitie'] = '';
        if (isset($message['vervangerErkenningsnummer']) === false)
            $message['vervangerErkenningsnummer'] = null;

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

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\FactuurMapper */
        $mapper = $this->get('appapi.mapper.factuur');

        $factuur = $factuurService->createFactuur($dagvergunning);
        $response = $mapper->singleEntityToModel($factuur);

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Geeft een nieuwe dagvergunnning uit
     *
     * @Method("POST")
     * @Route("/dagvergunning/")
     * @ApiDoc(
     *  section="Dagvergunning",
     *  parameters={
     *      {"name"="marktId", "dataType"="integer", "required"=true, "description"="ID van de markt"},
     *      {"name"="dag", "dataType"="string", "required"=true, "description"="Als yyyy-mm-dd"},
     *      {"name"="aantal3MeterKramen", "dataType"="integer", "required"=false, "description"="Aantal 3 meter kramen"},
     *      {"name"="aantal4MeterKramen", "dataType"="integer", "required"=false, "description"="Aantal 4 meter kramen"},
     *      {"name"="extraMeters", "dataType"="integer", "required"=false, "description"="Extra meters"},
     *      {"name"="aantalElektra", "dataType"="integer", "required"=false, "description"="Aantal elektra aansluitingen dat is afgenomen"},
     *      {"name"="afvaleiland", "dataType"="string", "required"="true"},
     *      {"name"="eenmaligElektra", "dataType"="boolean", "required"=false, "description"="Eenmalige elektra kosten ongeacht plekken"},
     *      {"name"="krachtstroom", "dataType"="boolean", "required"=false, "description"="Is er een krachtstroom aansluiting afgenomen?"},
     *      {"name"="reiniging", "dataType"="boolean", "required"=false, "description"="Is er reiniging afgenomen?"},
     *      {"name"="erkenningsnummer", "dataType"="string", "required"=true, "description"="Nummer zoals ingevoerd"},
     *      {"name"="vervangerErkenningsnummer", "dataType"="string", "required"=false, "description"="Nummer zoals ingevoerd"},
     *      {"name"="erkenningsnummerInvoerMethode", "dataType"="string", "required"=false, "description"="Waardes: handmatig, scan-foto, scan-nfc, scan-barcode, scan-qr, opgezocht, onbekend. Indien niet opgegeven wordt onbekend gebruikt."},
     *      {"name"="aanwezig", "dataType"="string", "required"=false, "description"="Aangetroffen persoon Zelf|Partner|Vervanger met toestemming|Vervanger zonder toestemming|Niet aanwezig|Niet geregisteerd"},
     *      {"name"="notitie", "dataType"="string", "required"=false, "description"="Vrij notitie veld"},
     *      {"name"="registratieDatumtijd", "dataType"="string", "required"=false, "description"="Datum/tijd dat de registratie is gemaakt, indien niet opgegeven wordt het moment van de request gebruikt"},
     *      {"name"="registratieGeolocatie", "dataType"="string", "required"=false, "description"="Geolocatie waar de registratie is ingevoerd, als lat,long"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function createAction(Request $request)
    {
        $message = json_decode($request->getContent(false), true);

        // check inputs
        if ($message === null)
            return new JsonResponse(['error' => json_last_error_msg()]);
        if (isset($message['marktId']) === false)
            return new JsonResponse(['error' => 'Required field marktId is missing']);
        if (isset($message['dag']) === false)
            return new JsonResponse(['error' => 'Required field dag is missing']);
        if (isset($message['erkenningsnummer']) === false)
            return new JsonResponse(['error' => 'Required field erkenningsnummer is missing']);
        if (isset($message['aanwezig']) === false)
            return new JsonResponse(['error' => 'Required field aanwezig is missing']);

        // set defaults
        if (isset($message['erkenningsnummerInvoerMethode']) === false)
            $message['erkenningsnummerInvoerMethode'] = 'onbekend';
        if (isset($message['registratieDatumtijd']) === false)
            $message['registratieDatumtijd'] = date('Y-m-d H:i:s');
        if (isset($message['registratieGeolocatie']) === false)
            $message['registratieGeolocatie'] = null;
        if (isset($message['aantal3MeterKramen']) === false)
            $message['aantal3MeterKramen'] = 0;
        if (isset($message['aantal4MeterKramen']) === false)
            $message['aantal4MeterKramen'] = 0;
        if (isset($message['extraMeters']) === false)
            $message['extraMeters'] = 0;
        if (isset($message['aantalElektra']) === false)
            $message['aantalElektra'] = 0;
        if (isset($message['afvaleiland']) === false)
            $message['afvaleiland'] = 0;
        if (isset($message['eenmaligElektra']) === false)
            $message['eenmaligElektra'] = false;
        if (isset($message['krachtstroom']) === false)
            $message['krachtstroom'] = false;
        if (isset($message['reiniging']) === false)
            $message['reiniging'] = false;
        if (isset($message['notitie']) === false)
            $message['notitie'] = '';
        if (isset($message['vervangerErkenningsnummer']) === false)
            $message['vervangerErkenningsnummer'] = null;

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

        $this->getDoctrine()->getManager()->persist($dagvergunning);
        $this->getDoctrine()->getManager()->flush();

        $factuurService = $this->get('appapi.factuurservice');
        $factuur = $factuurService->createFactuur($dagvergunning);
        $factuurService->saveFactuur($factuur);

        $result = $this->get('appapi.mapper.dagvergunning')->singleEntityToModel($dagvergunning);



        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Geeft dagvergunningen
     *
     * @Method("GET")
     * @Route("/dagvergunning/")
     * @ApiDoc(
     *  section="Dagvergunning",
     *  filters={
     *      {"name"="marktId", "dataType"="integer", "description"="ID van de markt"},
     *      {"name"="dag", "dataType"="string", "description"="Als yyyy-mm-dd"},
     *      {"name"="dagStart", "dateType"="string", "description"="Als yyyy-mm-dd, alleen i.c.m. dagEind"},
     *      {"name"="dagEind", "dateType"="string", "description"="Als yyyy-mm-dd, alleen i.c.m. dagStart"},
     *      {"name"="koopmanId", "dataType"="integer", "description"="Id van de koopman"},
     *      {"name"="erkenningsnummer", "dataType"="integer", "description"="Nummer van koopman waarop vergunning is uitgeschreven"},
     *      {"name"="doorgehaald", "dataType"="integer", "description"="Indien niet opgenomen of leeg of 0 enkel niet doorgehaalde dagvergunningen, indien opgenomen en 1 dan enkel doorgehaalde dagvergunningen"},
     *      {"name"="accountId", "dataType"="integer", "description"="Filter op de persoon die de dagvergunning uitgegeven heeft"},
     *      {"name"="listOffset", "dataType"="integer"},
     *      {"name"="listLength", "dataType"="integer", "description"="Default=100"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function listAction(Request $request)
    {
        /* @var $repo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\DagvergunningRepository */
        $repo = $this->get('appapi.repository.dagvergunning');

        $q = [];
        if ($request->query->has('marktId') === true)
            $q['marktId'] = $request->query->get('marktId');
        if ($request->query->has('dag') === true)
            $q['dag'] = $request->query->get('dag');
        if ($request->query->has('dagStart') === true && $request->query->has('dagEind') === true)
            $q['dagRange'] = [$request->query->get('dagStart'), $request->query->get('dagEind')];
        if ($request->query->has('koopmanId') === true)
            $q['koopmanId'] = $request->query->get('koopmanId');
        if ($request->query->has('erkenningsnummer') === true)
            $q['erkenningsnummer'] = str_replace('.', '', $request->query->get('erkenningsnummer'));
        if ($request->query->has('doorgehaald') === true)
            $q['doorgehaald'] = $request->query->get('doorgehaald');
        if ($request->query->has('accountId') === true)
            $q['accountId'] = $request->query->get('accountId');
        $results = $repo->search($q, $request->query->get('listOffset'), $request->query->get('listLength', 300));

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\DagvergunningMapper */
        $mapper = $this->get('appapi.mapper.dagvergunning');
        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Geeft dagvergunningen terug per koopman en datum
     *
     * @Method("GET")
     * @Route("/dagvergunning_by_date/{koopmanId}/{startDate}/{endDate}")
     * @ApiDoc(
     *  section="Dagvergunning",
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN') || has_role('ROLE_SENIOR')")
     */
    public function listByDateAction(Request $request, $koopmanId, $startDate, $endDate)
    {
        /* @var $repo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\DagvergunningRepository */
        $dagvergunningRepo = $this->get('appapi.repository.dagvergunning');

        $koopmanRepo = $this->get('appapi.repository.koopman');

        $koopman = $koopmanRepo->findOneById($koopmanId);

        if (null === $koopman) {
            throw $this->createNotFoundException('Can\'t find koopman with id ' . $koopmanId);
        }

        $sDate = new \DateTime($startDate . ' 00:00');
        $eDate = new \DateTime($endDate . ' 00:00');

        $results = $dagvergunningRepo->getByKoopmanAndDates($koopman, $sDate, $eDate);

         /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\DagvergunningMapper */
        $mapper = $this->get('appapi.mapper.dagvergunning');
        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Voert een doorhaling van de dagvergunning uit
     *
     * @Method("DELETE")
     * @Route("/dagvergunning/{id}")
     * @ApiDoc(
     *  section="Dagvergunning",
     *  requirements={
     *      {"name"="id", "dataType"="integer"},
     *  },
     *  parameters={
     *      {"name"="doorgehaaldDatumtijd", "dataType"="string", "required"=false, "description"="Datum/tijd dat de doorhaling is uitgevoerd, indien niet opgegeven wordt het moment van de request gebruikt"},
     *      {"name"="doorgehaaldGeolocatie", "dataType"="string", "required"=false, "description"="Geolocatie waar de doorhaling is uitgevoerd, als lat,long"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function deleteAction(Request $request, $id)
    {
        /* @var $repoDagvergunning \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\DagvergunningRepository */
        $repoDagvergunning = $this->get('appapi.repository.dagvergunning');

        // read message body
        $message = json_decode($request->getContent(false), true);

        // set message defaults
        if (isset($message['doorgehaaldDatumtijd']) === false)
            $message['doorgehaaldDatumtijd'] = date('Y-m-d H:i:s');
        if (isset($message['doorgehaaldGeolocatie']) === false)
            $message['doorgehaaldGeolocatie'] = null;

        // get object
        $dagvergunning = $repoDagvergunning->getById($id);
        if ($dagvergunning === null)
            throw $this->createNotFoundException('Can not find dagvergunning with id ' . $id);
        if ($dagvergunning->isDoorgehaald() === true)
            throw $this->createNotFoundException('Dagvergunning with id ' . $id . ' already doorgehaald');

        // modify object
        $dagvergunning->setDoorgehaald(true);
        $dagvergunning->setDoorgehaaldDatumtijd(\DateTime::createFromFormat('Y-m-d H:i:s', $message['doorgehaaldDatumtijd']));
        if ($message['doorgehaaldGeolocatie'] !== null && $message['doorgehaaldGeolocatie'] !== '')
        {
            $point = explode(',', $message['doorgehaaldGeolocatie']);
            $dagvergunning->setDoorgehaaldGeolocatie($point[0], $point[1]);
        }

        // set account
        $dagvergunning->setDoorgehaaldAccount($this->getUser());

        // save object
        $this->getDoctrine()->getManager()->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Geeft een nieuwe dagvergunnning uit
     *
     * @Method("PUT")
     * @Route("/dagvergunning/{id}")
     * @ApiDoc(
     *  section="Dagvergunning",
     *  parameters={
     *      {"name"="marktId", "dataType"="integer", "required"=true, "description"="ID van de markt"},
     *      {"name"="dag", "dataType"="string", "required"=true, "description"="Als yyyy-mm-dd"},
     *      {"name"="aantal3MeterKramen", "dataType"="integer", "required"=false, "description"="Aantal 3 meter kramen"},
     *      {"name"="aantal4MeterKramen", "dataType"="integer", "required"=false, "description"="Aantal 4 meter kramen"},
     *      {"name"="extraMeters", "dataType"="integer", "required"=false, "description"="Extra meters"},
     *      {"name"="aantalElektra", "dataType"="integer", "required"=false, "description"="Aantal elektra aansluitingen dat is afgenomen"},
     *      {"name"="afvaleiland", "dataType"="string", "required"="true"},
     *      {"name"="eenmaligElektra", "dataType"="boolean", "required"=false, "description"="Eenmalige elektra kosten ongeacht plekken"},
     *      {"name"="krachtstroom", "dataType"="boolean", "required"=false, "description"="Is er een krachtstroom aansluiting afgenomen?"},
     *      {"name"="reiniging", "dataType"="boolean", "required"=false, "description"="Is er reiniging afgenomen?"},
     *      {"name"="erkenningsnummer", "dataType"="string", "required"=true, "description"="Nummer zoals ingevoerd"},
     *      {"name"="vervangerErkenningsnummer", "dataType"="string", "required"=false, "description"="Nummer zoals ingevoerd"},
     *      {"name"="erkenningsnummerInvoerMethode", "dataType"="string", "required"=false, "description"="Waardes: handmatig, scan-foto, scan-nfc, scan-barcode, scan-qr, opgezocht, onbekend. Indien niet opgegeven wordt onbekend gebruikt."},
     *      {"name"="aanwezig", "dataType"="string", "required"=false, "description"="Aangetroffen persoon Zelf|Partner|Vervanger met toestemming|Vervanger zonder toestemming|Niet aanwezig|Niet geregisteerd"},
     *      {"name"="notitie", "dataType"="string", "required"=false, "description"="Vrij notitie veld"},
     *      {"name"="registratieDatumtijd", "dataType"="string", "required"=false, "description"="Datum/tijd dat de registratie is gemaakt, indien niet opgegeven wordt het moment van de request gebruikt"},
     *      {"name"="registratieGeolocatie", "dataType"="string", "required"=false, "description"="Geolocatie waar de registratie is ingevoerd, als lat,long"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function updateAction(Request $request, $id)
    {
        $request->query->set('doorgehaaldDatumtijd', $request->query->get('registratieDatumtijd'));
        $request->query->set('doorgehaaldGeolocatie', $request->query->get('registratieGeolocatie'));

        $this->deleteAction($request, $id);

        //////

        $message = json_decode($request->getContent(false), true);

        // check inputs
        if ($message === null)
            return new JsonResponse(['error' => json_last_error_msg()]);
        if (isset($message['marktId']) === false)
            return new JsonResponse(['error' => 'Required field marktId is missing']);
        if (isset($message['dag']) === false)
            return new JsonResponse(['error' => 'Required field dag is missing']);
        if (isset($message['erkenningsnummer']) === false)
            return new JsonResponse(['error' => 'Required field erkenningsnummer is missing']);
        if (isset($message['aanwezig']) === false)
            return new JsonResponse(['error' => 'Required field aanwezig is missing']);

        // set defaults
        if (isset($message['erkenningsnummerInvoerMethode']) === false)
            $message['erkenningsnummerInvoerMethode'] = 'onbekend';
        if (isset($message['registratieDatumtijd']) === false)
            $message['registratieDatumtijd'] = date('Y-m-d H:i:s');
        if (isset($message['registratieGeolocatie']) === false)
            $message['registratieGeolocatie'] = null;
        if (isset($message['aantal3MeterKramen']) === false)
            $message['aantal3MeterKramen'] = 0;
        if (isset($message['aantal4MeterKramen']) === false)
            $message['aantal4MeterKramen'] = 0;
        if (isset($message['extraMeters']) === false)
            $message['extraMeters'] = 0;
        if (isset($message['aantalElektra']) === false)
            $message['aantalElektra'] = 0;
        if (isset($message['afvaleiland']) === false)
            $message['afvaleiland'] = 0;
        if (isset($message['eenmaligElektra']) === false)
            $message['eenmaligElektra'] = false;
        if (isset($message['krachtstroom']) === false)
            $message['krachtstroom'] = false;
        if (isset($message['reiniging']) === false)
            $message['reiniging'] = false;
        if (isset($message['notitie']) === false)
            $message['notitie'] = '';
        if (isset($message['vervangerErkenningsnummer']) === false)
            $message['vervangerErkenningsnummer'] = null;

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

        $this->getDoctrine()->getManager()->persist($dagvergunning);
        $this->getDoctrine()->getManager()->flush();

        $factuurService = $this->get('appapi.factuurservice');
        $factuur = $factuurService->createFactuur($dagvergunning);
        $factuurService->saveFactuur($factuur);

        $result = $this->get('appapi.mapper.dagvergunning')->singleEntityToModel($dagvergunning);

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Haal de details van een dagvergunning op
     *
     * @Method("GET")
     * @Route("/dagvergunning/{id}")
     * @ApiDoc(
     *  section="Dagvergunning",
     *  requirements={
     *      {"name"="id", "dataType"="integer"},
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function detailAction(Request $request, $id)
    {
        /* @var $repoDagvergunning \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\DagvergunningRepository */
        $repoDagvergunning = $this->get('appapi.repository.dagvergunning');

        $dagvergunning = $repoDagvergunning->getById($id);

        $result = $this->get('appapi.mapper.dagvergunning')->singleEntityToModel($dagvergunning);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
