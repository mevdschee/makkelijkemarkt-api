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

use App\Entity\Markt;
use App\Model\SimpleMarktModel;

class SimpleMarktMapper
{
    /**
     * @param Markt $e
     * @return \App\Model\SimpleMarktModel
     */
    public function singleEntityToModel(Markt $e)
    {
        $object = new SimpleMarktModel();
        $object->id = $e->getId();
        $object->naam = $e->getNaam();
        $object->afkorting = $e->getAfkorting();
        return $object;
    }

    /**
     * @param \App\Entity\Markt $list
     * @return \App\Model\SimpleMarktModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e Markt */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }

}
