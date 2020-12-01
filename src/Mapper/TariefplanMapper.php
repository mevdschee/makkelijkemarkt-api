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

use App\Entity\Tariefplan;
use App\Model\TariefplanModel;

class TariefplanMapper
{
    /**
     * @var LineairplanMapper
     */
    protected $lineairplanMapper;

    /**
     * @var ConcreetplanMapper
     */
    protected $concreetplanMapper;

    public function __construct($lineairplanMapper, $concreetplanMapper)
    {
        $this->lineairplanMapper = $lineairplanMapper;
        $this->concreetplanMapper = $concreetplanMapper;
    }

    /**
     * @param Tariefplan $e
     * @return TariefPlanModel
     */
    public function singleEntityToModel(Tariefplan $e)
    {
        $object = new TariefplanModel();
        $object->id = $e->getId();
        $object->naam = $e->getNaam();
        $object->geldigVanaf = $e->getGeldigVanaf();
        $object->geldigTot = $e->getGeldigTot();
        $markt = $e->getMarkt();
        $object->marktId = $markt->getId();
        $lineairPlan = $e->getLineairplan();
        $object->lineairplan = null === $lineairPlan ? null : $this->lineairplanMapper->singleEntityToModel($lineairPlan);
        $concreetPlan = $e->getConcreetplan();
        $object->concreetplan = null === $concreetPlan ? null : $this->concreetplanMapper->singleEntityToModel($concreetPlan);
        return $object;
    }

    /**
     * @param Tariefplan[] $list
     * @return TariefplanModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e Tariefplan */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}
