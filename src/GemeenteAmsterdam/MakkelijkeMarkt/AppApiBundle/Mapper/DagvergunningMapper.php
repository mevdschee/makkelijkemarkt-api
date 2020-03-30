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

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\DagvergunningModel;

class DagvergunningMapper
{
    /**
     * @var MarktMapper
     */
    protected $mapperMarkt;

    /**
     * @var KoopmanMapper
     */
    protected $mapperKoopman;

    /**
     * @var SollicitatieMapper
     */
    protected $mapperSollicitatie;

    /**
     * @var FactuurMapper
     */
    protected $mapperFactuur;

    /**
     * @var AccountMapper
     */
    protected $mapperAccount;

    /**
     * @var VergunningControleMapper
     */
    protected $mapperVergunningControle;

    public function __construct(
        MarktMapper $marktMapper,
        KoopmanMapper $koopmanMapper,
        SollicitatieMapper $sollicitatieMapper,
        AccountMapper $accountMapper,
        FactuurMapper $factuurMapper,
        VergunningControleMapper $vergunningControleMapper
    )
    {
        $this->mapperMarkt              = $marktMapper;
        $this->mapperKoopman            = $koopmanMapper;
        $this->mapperSollicitatie       = $sollicitatieMapper;
        $this->mapperAccount            = $accountMapper;
        $this->mapperFactuur            = $factuurMapper;
        $this->mapperVergunningControle = $vergunningControleMapper;
    }

    /**
     * @param Dagvergunning $e
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\DagvergunningModel
     */
    public function singleEntityToModel(Dagvergunning $e)
    {
        $object = new DagvergunningModel();
        $object->id = $e->getId();
        $object->erkenningsnummer = $e->getErkenningsnummerInvoerWaarde();
        $object->erkenningsnummerInvoerMethode = $e->getErkenningsnummerInvoerMethode();
        $object->registratieGeolocatie = $e->getRegistratieGeolocatie();
        if ($e->getKoopman() !== null)
            $object->koopman = $this->mapperKoopman->singleEntityToSimpleModel($e->getKoopman());
        if ($e->getVervanger() !== null)
            $object->vervanger = $this->mapperKoopman->singleEntityToSimpleModel($e->getVervanger());
        if ($e->getMarkt() !== null)
            $object->markt = $this->mapperMarkt->singleEntityToSimpleModel($e->getMarkt());
        $object->aantal3MeterKramen = $e->getAantal3MeterKramen();
        $object->aantal4MeterKramen = $e->getAantal4MeterKramen();
        $object->afvaleiland = $e->getAfvaleiland();
        $object->eenmaligElektra = $e->getEenmaligElektra();
        $object->extraMeters = $e->getExtraMeters();
        $object->aantalElektra = $e->getAantalElektra();
        $object->krachtstroom = boolval($e->getKrachtstroom());
        $object->reiniging = boolval($e->getReiniging());
        $object->dag = $e->getDag()->format('Y-m-d');
        $object->aanwezig = $e->getAanwezig();
        $object->registratieDatumtijd = $e->getRegistratieDatumtijd()->format('Y-m-d H:i:s');
        $object->aanmaakDatumtijd = $e->getAanmaakDatumtijd()->format('Y-m-d H:i:s');
        if ($e->getVerwijderdDatumtijd() !== null)
            $object->verwijderdDatumtijd = $e->getVerwijderdDatumtijd()->format('Y-m-d H:i:s');
        if ($e->getDoorgehaaldDatumtijd() !== null)
            $object->doorgehaaldDatumtijd = $e->getDoorgehaaldDatumtijd()->format('Y-m-d H:i:s');
        $object->notitie = $e->getNotitie();
        $object->aantal3meterKramenVast = $e->getAantal3meterKramenVast();
        $object->aantal4meterKramenVast = $e->getAantal4meterKramenVast();
        $object->aantalExtraMetersVast = $e->getAantalExtraMetersVast();
        $object->aantalElektraVast = $e->getAantalElektraVast();
        $object->krachtstroomVast = $e->getKrachtstroomVast();
        $object->afvaleilandVast = $e->getAfvaleilandVast();
        $object->loten = $e->getLoten();
        $object->audit = $e->getAudit();
        $object->auditReason = $e->getAuditReason();
        $object->status = $e->getStatusSolliciatie();
        if ($e->getSollicitatie() !== null)
            $object->sollicitatie = $this->mapperSollicitatie->singleEntityToSimpleModel($e->getSollicitatie());
        $object->doorgehaald = $e->isDoorgehaald();
        $object->totaleLengte = ($e->getAantal3MeterKramen() * 3) + ($e->getAantal4MeterKramen() * 4) + $e->getExtraMeters();
        $object->totaleLengteVast = ($e->getAantal3meterKramenVast() * 3) + ($e->getAantal4meterKramenVast() * 4) + $e->getExtraMeters();
        if ($e->getRegistratieAccount() !== null)
            $object->registratieAccount = $this->mapperAccount->singleEntityToModel($e->getRegistratieAccount());
        if ($e->getDoorgehaaldAccount() !== null)
            $object->doorgehaaldAccount = $this->mapperAccount->singleEntityToModel($e->getDoorgehaaldAccount());
        $factuur = $e->getFactuur();
        $object->factuur = null !== $factuur ? $this->mapperFactuur->singleEntityToModel($factuur) : null;

        if (count($e->getVergunningControles())) {
            $object->controles = $this->mapperVergunningControle->multipleEntityToModel($e->getVergunningControles());
        }

        return $object;
    }

    /**
     * @param multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning $list
     * @return multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\DagvergunningModel
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e Koopman */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }

}