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

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\VergunningControle;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Service\FactuurService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning;

/**
 * @Route("1.1.0")
 */
class VergunningControleController extends Controller
{
    /**
     * Maak een vergunning controle
     *
     * @Method("POST")
     * @Route("/controle/")
     * @ApiDoc(
     *  section="Controle",
     *  parameters={
     *      {"name"="dagvergunningId", "dataType"="integer", "required"=true, "description"="ID van de dagvergunning"},
     *      {"name"="aanwezig", "dataType"="string", "required"=false, "description"="Aangetroffen persoon Zelf|Partner|Vervanger met toestemming|Vervanger zonder toestemming|Niet aanwezig|Niet geregisteerd"},
     *      {"name"="registratieGeolocatie", "dataType"="string", "required"=false, "description"="Geolocatie waar de registratie is ingevoerd, als lat,long"},
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
     *      {"name"="registratieGeolocatie", "dataType"="string", "required"=false, "description"="Geolocatie waar de registratie is ingevoerd, als lat,long"},
     *      {"name"="ronde", "dataType"="int", "required"=false, "description"="Ronde nummer"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function createAction(Request $request)
    {
        $message = json_decode($request->getContent(false), true);
        if (null === $message) {
            return new JsonResponse(['error' => json_last_error_msg()]);
        }
        if (false === isset($message['dagvergunningId'])) {
            return new JsonResponse(['error' => 'Required field dagvergunningId is missing']);
        }
        if (false === isset($message['aanwezig'])) {
            return new JsonResponse(['error' => 'Required field aanwezig is missing']);
        }
        if (false === isset($message['registratieGeolocatie'])) {
            return new JsonResponse(['error' => 'Required field registratieGeolocatie is missing']);
        }
        if (isset($message['erkenningsnummer']) === false) {
            return new JsonResponse(['error' => 'Required field erkenningsnummer is missing']);
        }
        if (isset($message['ronde']) === false) {
            return new JsonResponse(['error' => 'Required field ronde is missing']);
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

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Dagvergunning::class);

        $dagvergunning = $repo->find($message['dagvergunningId']);
        if (null === $dagvergunning) {
            return new JsonResponse(['error' => 'dagvergunningId not in DB']);
        }

        $controle = new VergunningControle();
        $controle->setRegistratieAccount($this->getUser());
        $controle->setDagvergunning($dagvergunning);
        $dagvergunning->addVergunningControle($controle);

        $controle = $this->map(
            $controle,
            $message['aanwezig'],
            $message['erkenningsnummer'],
            $message['erkenningsnummerInvoerMethode'],
            $message['vervangerErkenningsnummer'],
            $message['registratieGeolocatie'],
            $message['aantal3MeterKramen'],
            $message['aantal4MeterKramen'],
            $message['extraMeters'],
            $message['aantalElektra'],
            $message['eenmaligElektra'],
            $message['afvaleiland'],
            $message['krachtstroom'],
            $message['reiniging'],
            $message['notitie'],
            $message['ronde']
        );

        $em->persist($controle);
        $em->flush();

        $result = $this->get('appapi.mapper.dagvergunning')->singleEntityToModel($dagvergunning);

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Update een vergunning controle
     *
     * @Method("PUT")
     * @Route("/controle/{controleId}")
     * @ApiDoc(
     *  section="Controle",
     *  parameters={
     *      {"name"="aanwezig", "dataType"="string", "required"=false, "description"="Aangetroffen persoon Zelf|Partner|Vervanger met toestemming|Vervanger zonder toestemming|Niet aanwezig|Niet geregisteerd"},
     *      {"name"="registratieGeolocatie", "dataType"="string", "required"=false, "description"="Geolocatie waar de registratie is ingevoerd, als lat,long"},
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
     *      {"name"="registratieGeolocatie", "dataType"="string", "required"=false, "description"="Geolocatie waar de registratie is ingevoerd, als lat,long"},
     *      {"name"="ronde", "dataType"="int", "required"=false, "description"="Ronde nummer"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function updateAction(Request $request, $controleId)
    {
        $message = json_decode($request->getContent(false), true);
        if (null === $message) {
            return new JsonResponse(['error' => json_last_error_msg()]);
        }
        if (false === isset($message['aanwezig'])) {
            return new JsonResponse(['error' => 'Required field aanwezig is missing']);
        }
        if (false === isset($message['registratieGeolocatie'])) {
            return new JsonResponse(['error' => 'Required field registratieGeolocatie is missing']);
        }
        if (isset($message['erkenningsnummer']) === false) {
            return new JsonResponse(['error' => 'Required field erkenningsnummer is missing']);
        }
        if (isset($message['ronde']) === false) {
            return new JsonResponse(['error' => 'Required field ronde is missing']);
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

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(VergunningControle::class);

        $controle = $repo->find($controleId);
        if (null === $controle) {
            return new JsonResponse(['error' => 'controleId not in DB']);
        }

        $controle = $this->map(
            $controle,
            $message['aanwezig'],
            $message['erkenningsnummer'],
            $message['erkenningsnummerInvoerMethode'],
            $message['vervangerErkenningsnummer'],
            $message['registratieGeolocatie'],
            $message['aantal3MeterKramen'],
            $message['aantal4MeterKramen'],
            $message['extraMeters'],
            $message['aantalElektra'],
            $message['eenmaligElektra'],
            $message['afvaleiland'],
            $message['krachtstroom'],
            $message['reiniging'],
            $message['notitie'],
            $message['ronde']
        );

        $em->flush();

        $dagvergunning = $controle->getDagvergunning();

        $result = $this->get('appapi.mapper.dagvergunning')->singleEntityToModel($dagvergunning);

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * @param VergunningControle $vergunningControle
     * @param $aanwezig
     * @param $erkenningsnummer
     * @param $erkenningsnummerInvoerMethode
     * @param $vervangerErkenningsnummer
     * @param $registratieGeolocatie
     * @param $aantal3MeterKramen
     * @param $aantal4MeterKramen
     * @param $extraMeters
     * @param $aantalElektra
     * @param $eenmaligElektra
     * @param $afvaleiland
     * @param $krachtstroom
     * @param $reiniging
     * @param $notitie
     * @param $ronde
     * @return VergunningControle
     */
    private function map(
        VergunningControle $vergunningControle,
        $aanwezig,
        $erkenningsnummer,
        $erkenningsnummerInvoerMethode,
        $vervangerErkenningsnummer,
        $registratieGeolocatie,
        $aantal3MeterKramen,
        $aantal4MeterKramen,
        $extraMeters,
        $aantalElektra,
        $eenmaligElektra,
        $afvaleiland,
        $krachtstroom,
        $reiniging,
        $notitie,
        $ronde
    ) {
        $em = $this->getDoctrine()->getManager();
        $repoKoopman = $em->getRepository(Koopman::class);
        $repoSollicitatie = $em->getRepository(Sollicitatie::class);

        // set aanwezig
        $vergunningControle->setAanwezig($aanwezig);

        // set erkenningsnummer info
        $vergunningControle->setErkenningsnummerInvoerWaarde(str_replace('.', '', $erkenningsnummer));
        $vergunningControle->setErkenningsnummerInvoerMethode($erkenningsnummerInvoerMethode);

        if (null !== $vervangerErkenningsnummer) {
            $vervanger = $repoKoopman->getByErkenningsnummer(str_replace('.', '', $vervangerErkenningsnummer));
            if ($vervanger !== null) {
                $vergunningControle->setVervanger($vervanger);
            }
        } else {
            $vergunningControle->setVervanger(null);
        }

        // set geolocatie
        $point = FactuurService::parseGeolocation($registratieGeolocatie);
        $vergunningControle->setRegistratieGeolocatie($point[0], $point[1]);

        $now = new \DateTime();

        $vergunningControle->setRegistratieDatumtijd($now);

        // set account
        $vergunningControle->setRegistratieAccount($this->getUser());

        // extras
        $vergunningControle->setAantal3MeterKramen(intval($aantal3MeterKramen));
        $vergunningControle->setAantal4MeterKramen(intval($aantal4MeterKramen));
        $vergunningControle->setExtraMeters(intval($extraMeters));
        $vergunningControle->setAantalElektra(intval($aantalElektra));
        $vergunningControle->setEenmaligElektra(boolval($eenmaligElektra));
        $vergunningControle->setAfvaleiland(intval($afvaleiland));
        $vergunningControle->setKrachtstroom(boolval($krachtstroom));
        $vergunningControle->setReiniging(boolval($reiniging));
        $vergunningControle->setNotitie($notitie);
        $vergunningControle->setRonde($ronde);

        // sollicitatie koppeling
        $sollicitatie = $repoSollicitatie->getByMarktAndErkenningsNummer(
            $vergunningControle->getDagvergunning()->getMarkt(),
            $erkenningsnummer,
            false
        );
        if ($sollicitatie !== null) {
            $vergunningControle->setAantal3meterKramenVast($sollicitatie->getAantal3MeterKramen());
            $vergunningControle->setAantal4meterKramenVast($sollicitatie->getAantal4MeterKramen());
            $vergunningControle->setAantalExtraMetersVast($sollicitatie->getAantalExtraMeters());
            $vergunningControle->setAantalElektraVast($sollicitatie->getAantalElektra());
            $vergunningControle->setKrachtstroomVast($sollicitatie->getKrachtstroom());
            $vergunningControle->setAfvaleilandVast($sollicitatie->getAantalAfvaleilanden());
            $vergunningControle->setSollicitatie($sollicitatie);
        }
        $vergunningControle->setStatusSolliciatie($sollicitatie !== null ? $sollicitatie->getStatus() : 'lot');

        return $vergunningControle;
    }
}
