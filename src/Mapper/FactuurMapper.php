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

use App\Entity\Factuur;
use App\Model\FactuurModel;
use App\Service\FactuurService;

class FactuurMapper
{
    /**
     * @var ProductMapper
     */
    protected $mapperProduct;

    /**
     * @var FactuurService
     */
    protected $factuurService;

    public function __construct(ProductMapper $mapperProduct, FactuurService $factuurService)
    {
        $this->mapperProduct = $mapperProduct;
        $this->factuurService = $factuurService;
    }

    /**
     * @param Factuur $e
     * @return \App\Model\FactuurModel
     */
    public function singleEntityToModel(Factuur $e)
    {
        $object = new FactuurModel();
        $object->id = $e->getId();

        $object->totaal = $this->factuurService->getTotaal($e);
        $object->exclusief = $this->factuurService->getTotaal($e, false);

        $object->producten = [];
        $producten = $e->getProducten();
        $arr = $producten->toArray();
        foreach ($arr as $product) {
            $object->producten[] = $this->mapperProduct->singleEntityToModel($product);
        }
        return $object;
    }

    /**
     * @param \App\Entity\Factuur $list
     * @return \App\Model\FactuurModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e Factuur */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}
