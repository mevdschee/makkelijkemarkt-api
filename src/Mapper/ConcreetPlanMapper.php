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

use App\Entity\Concreetplan;
use App\Model\ConcreetplanModel;

class ConcreetPlanMapper
{
    /**
     * @param Concreetplan $e
     * @return ConcreetplanModel
     */
    public function singleEntityToModel(Concreetplan $e)
    {
        $object = new ConcreetplanModel();
        $object->id = $e->getId();
        $object->een_meter = $e->getEenMeter();
        $object->drie_meter = $e->getDrieMeter();
        $object->vier_meter = $e->getVierMeter();
        $object->elektra = $e->getElektra();
        $object->promotieGeldenPerMeter = $e->getPromotieGeldenPerMeter();
        $object->promotieGeldenPerKraam = $e->getPromotieGeldenPerKraam();
        $object->afvaleiland = $e->getAfvaleiland();
        $object->eenmaligElektra = $e->getEenmaligElektra();

        return $object;
    }

    /**
     * @param Concreetplan[] $list
     * @return ConcreetplanModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e Concreetplan */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}
