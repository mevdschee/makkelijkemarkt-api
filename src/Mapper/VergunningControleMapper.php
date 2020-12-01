<?php
/*
 *  Copyright (c) 2020 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace App\Mapper;

use App\Entity\VergunningControle;
use App\Model\VergunningControleModel;

class VergunningControleMapper
{
    /**
     * @var SimpleKoopmanMapper
     */
    private $mapperSimpleKoopman;

    /**
     * @var SimpleSollicitatieMapper
     */
    private $mapperSimpleSollicitatie;

    /**
     * @var AccountMapper
     */
    private $mapperAccount;

    public function __construct(
        SimpleKoopmanMapper $simpleKoopmanMapper,
        SimpleSollicitatieMapper $simpleSollicitatieMapper,
        AccountMapper $accountMapper
    ) {
        $this->mapperSimpleKoopman = $simpleKoopmanMapper;
        $this->mapperSimpleSollicitatie = $simpleSollicitatieMapper;
        $this->mapperAccount = $accountMapper;
    }

    /**
     * @param VergunningControle $e
     * @return VergunningControleModel
     */
    public function singleEntityToModel(VergunningControle $e)
    {
        $object = new VergunningControleModel();
        $object->id = $e->getId();
        $object->erkenningsnummer = $e->getErkenningsnummerInvoerWaarde();
        $object->erkenningsnummerInvoerMethode = $e->getErkenningsnummerInvoerMethode();
        $object->registratieGeolocatie = $e->getRegistratieGeolocatie();
        if ($e->getVervanger() !== null) {
            $object->vervanger = $this->mapperSimpleKoopman->singleEntityToModel($e->getVervanger());
        }
        $object->aantal3MeterKramen = $e->getAantal3MeterKramen();
        $object->aantal4MeterKramen = $e->getAantal4MeterKramen();
        $object->afvaleiland = $e->getAfvaleiland();
        $object->eenmaligElektra = $e->getEenmaligElektra();
        $object->extraMeters = $e->getExtraMeters();
        $object->aantalElektra = $e->getAantalElektra();
        $object->krachtstroom = boolval($e->getKrachtstroom());
        $object->reiniging = boolval($e->getReiniging());
        $object->aanwezig = $e->getAanwezig();
        $object->registratieDatumtijd = $e->getRegistratieDatumtijd()->format('Y-m-d H:i:s');
        $object->notitie = $e->getNotitie();
        $object->aantal3meterKramenVast = $e->getAantal3meterKramenVast();
        $object->aantal4meterKramenVast = $e->getAantal4meterKramenVast();
        $object->aantalExtraMetersVast = $e->getAantalExtraMetersVast();
        $object->aantalElektraVast = $e->getAantalElektraVast();
        $object->krachtstroomVast = $e->getKrachtstroomVast();
        $object->afvaleilandVast = $e->getAfvaleilandVast();
        $object->status = $e->getStatusSolliciatie();
        $object->ronde = $e->getRonde();
        if ($e->getSollicitatie() !== null) {
            $object->sollicitatie = $this->mapperSimpleSollicitatie->singleEntityToModel($e->getSollicitatie());
        }
        $object->totaleLengte = ($e->getAantal3MeterKramen() * 3) + ($e->getAantal4MeterKramen() * 4) + $e->getExtraMeters();
        $object->totaleLengteVast = ($e->getAantal3meterKramenVast() * 3) + ($e->getAantal4meterKramenVast() * 4) + $e->getExtraMeters();
        if ($e->getRegistratieAccount() !== null) {
            $object->registratieAccount = $this->mapperAccount->singleEntityToModel($e->getRegistratieAccount());
        }

        return $object;
    }

    /**
     * @param VergunningControle[] $list
     * @return VergunningControleModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e VergunningControle */
            $result[] = $this->singleEntityToModel($e);
        }

        return $result;
    }
}
