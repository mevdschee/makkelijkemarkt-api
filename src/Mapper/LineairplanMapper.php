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

use App\Entity\Lineairplan;
use App\Model\LineairplanModel;

class LineairplanMapper
{
    /**
     * @param Lineairplan $e
     * @return LineairPlanModel
     */
    public function singleEntityToModel(Lineairplan $e)
    {
        $object = new LineairplanModel();
        $object->id = $e->getId();
        $object->tariefPerMeter = $e->getTariefPerMeter();
        $object->reinigingPerMeter = $e->getReinigingPerMeter();
        $object->toeslagBedrijfsafvalPerMeter = $e->getToeslagBedrijfsafvalPerMeter();
        $object->toeslagKrachtstroomPerAansluiting = $e->getToeslagKrachtstroomPerAansluiting();
        $object->promotieGeldenPerMeter = $e->getPromotieGeldenPerMeter();
        $object->promotieGeldenPerKraam = $e->getPromotieGeldenPerKraam();
        $object->afvaleiland = $e->getAfvaleiland();
        $object->eenmaligElektra = $e->getEenmaligElektra();
        $object->elektra = $e->getElektra();

        return $object;
    }

    /**
     * @param Lineairplan[] $list
     * @return LineairplanModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e Lineairplan */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}
