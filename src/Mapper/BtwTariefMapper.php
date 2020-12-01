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

use App\Entity\BtwTarief;
use App\Model\BtwTariefModel;

class BtwTariefMapper
{
    /**
     * @param BtwTarief $e
     * @return \App\Model\BtwTariefModel
     */
    public function singleEntityToModel(BtwTarief $e)
    {
        $object = new BtwTariefModel();
        $object->id = $e->getId();
        $object->jaar = $e->getJaar();
        $object->hoog = $e->getHoog();
        return $object;
    }

    /**
     * @param \App\Entity\BtwTarief[] $list
     * @return \App\Model\BtwTariefModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e BtwTarief */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}
