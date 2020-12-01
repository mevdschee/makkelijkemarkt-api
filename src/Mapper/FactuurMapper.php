<?php

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
