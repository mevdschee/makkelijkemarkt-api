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

use App\Entity\Product;
use App\Model\ProductModel;

class ProductMapper
{

    /**
     * @param Product $e
     * @return \App\Model\ProductModel
     */
    public function singleEntityToModel(Product $e)
    {
        $object = new ProductModel();
        $object->id = $e->getId();
        $object->naam = $e->getNaam();
        $object->bedrag = $e->getBedrag();
        $object->aantal = $e->getAantal();
        $object->totaal = number_format($e->getAantal() * $e->getBedrag(), 2);
        $object->btw_percentage = $e->getBtwHoog();
        $object->btw_totaal = number_format($e->getAantal() * $e->getBedrag() * ($e->getBtwHoog() / 100), 2);
        $object->totaal_inclusief = number_format($e->getAantal() * $e->getBedrag() * ($e->getBtwHoog() / 100 + 1), 2);

        return $object;
    }

    /**
     * @param \App\Entity\Product $list
     * @return \App\Model\ProductModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e Product */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}
