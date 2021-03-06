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

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Notitie;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\NotitieModel;

class NotitieMapper
{
    /**
     * @var MarktMapper
     */
    protected $mapperMarkt;

    /**
     * @param MarktMapper $mapperMarkt
     */
    public function setMarktMapper(MarktMapper $mapperMarkt)
    {
        $this->mapperMarkt = $mapperMarkt;
    }

    /**
     * @param Notitie $e
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\NotitieModel
     */
    public function singleEntityToModel(Notitie $e)
    {
        $object = new NotitieModel();
        $object->id = $e->getId();
        $object->aangemaaktDatumtijd = $e->getAangemaaktDatumtijd()->format('Y-m-d H:i:s');
        $object->aangemaaktGeolocatie = $e->getAangemaaktGeolocatie();
        $object->afgevinktDatumtijd = $e->getAfgevinktDatumtijd() === null ? null : $e->getAfgevinktDatumtijd()->format('Y-m-d H:i:s');
        $object->afgevinktStatus = $e->getAfgevinktStatus();
        $object->bericht = $e->getBericht();
        $object->dag = $e->getDag()->format('Y-m-d');
        $object->markt = $this->mapperMarkt->singleEntityToSimpleModel($e->getMarkt());
        $object->verwijderdDatumtijd = $e->getVerwijderdDatumtijd() === null ? null : $e->getVerwijderdDatumtijd()->format('Y-m-d H:i:s');
        $object->verwijderdStatus = $e->getVerwijderd();
        return $object;
    }

    /**
     * @param multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Notitie[] $list
     * @return multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\NotitieModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e Notitie */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}