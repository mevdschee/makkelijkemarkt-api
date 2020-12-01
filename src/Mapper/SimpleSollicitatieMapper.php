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

use App\Entity\Sollicitatie;
use App\Model\SimpleSollicitatieModel;

class SimpleSollicitatieMapper
{
    /**
     * @var SimpleMarktMapper
     */
    private $mapperSimpleMarkt;

    /**
     * @param SimpleMarktMapper $mapperSimpleMarkt
     */
    public function __construct(SimpleMarktMapper $mapperSimpleMarkt)
    {
        $this->mapperSimpleMarkt = $mapperSimpleMarkt;
    }

    /**
     * @param Sollicitatie $e
     * @return \App\Model\SimpleSollicitatieModel
     */
    public function singleEntityToModel(Sollicitatie $e)
    {
        $object = new SimpleSollicitatieModel();
        $object->id = $e->getId();
        $object->sollicitatieNummer = $e->getSolliciatieNummer();
        $object->status = $e->getStatus();
        $object->aantal3MeterKramen = $e->getAantal3MeterKramen();
        $object->aantal4MeterKramen = $e->getAantal4MeterKramen();
        $object->aantalExtraMeters = $e->getAantalExtraMeters();
        $object->aantalElektra = $e->getAantalElektra();
        $object->aantalAfvaleiland = $e->getAantalAfvaleilanden();
        $object->vastePlaatsen = $e->getVastePlaatsen();
        $object->krachtstroom = $e->getKrachtstroom();
        $object->doorgehaald = $e->getDoorgehaald();
        $object->doorgehaaldReden = $e->getDoorgehaaldReden();
        $object->markt = $this->mapperSimpleMarkt->singleEntityToModel($e->getMarkt());
        $object->koppelveld = $e->getKoppelveld();
        return $object;
    }

    /**
     * @param \App\Entity\Sollicitatie $list
     * @return \App\Model\SimpleSollicitatieModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e Sollicitatie */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }

}
