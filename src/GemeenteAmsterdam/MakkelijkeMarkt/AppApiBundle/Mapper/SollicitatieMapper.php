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

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\SollicitatieModel;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\SimpleSollicitatieModel;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie;

class SollicitatieMapper
{
    /**
     * @var MarktMapper
     */
    private $mapperMarkt;

    /**
     * @var KoopmanMapper
     */
    private $mapperKoopman;

    /**
     * @param MarktMapper $mapperMarkt
     */
    public function setMarktMapper(MarktMapper $mapperMarkt)
    {
        $this->mapperMarkt = $mapperMarkt;
    }

    /**
     * @param KoopmanMapper $mapperKoopman
     */
    public function setKoopmanMapper(KoopmanMapper $mapperKoopman)
    {
        $this->mapperKoopman = $mapperKoopman;
    }

    /**
     * @param Sollicitatie $e
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\SollicitatieModel
     */
    public function singleEntityToModel(Sollicitatie $e)
    {
        $object = new SollicitatieModel();
        $object->id = $e->getId();
        $object->koopman = $this->mapperKoopman->singleEntityToSimpleModel($e->getKoopman());
        $object->markt = $this->mapperMarkt->singleEntityToSimpleModel($e->getMarkt());
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
        $object->koppelveld = $e->getKoppelveld();
        return $object;
    }

    /**
     * @param Sollicitatie $e
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\SimpleSollicitatieModel
     */
    public function singleEntityToSimpleModel(Sollicitatie $e)
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
        $object->markt = $this->mapperMarkt->singleEntityToSimpleModel($e->getMarkt());
        $object->koppelveld = $e->getKoppelveld();
        return $object;
    }

    /**
     * @param multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie $list
     * @return multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\SollicitatieModel
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e Sollicitatie */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }

    /**
     * @param multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie $list
     * @return multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\SimpleSollicitatieModel
     */
    public function multipleEntityToSimpleModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e Sollicitatie */
            $result[] = $this->singleEntityToSimpleModel($e);
        }
        return $result;
    }

}