<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper;


use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Concreetplan;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\ConcreetplanModel;

class ConcreetPlanMapper
{
    /**
     * @param Concreetplan $e
     * @return ConcreetplanModel
     */
    public function singleEntityToModel(Concreetplan $e)
    {
        $object                         = new ConcreetplanModel();
        $object->id                     = $e->getId();
        $object->een_meter              = $e->getEenMeter();
        $object->drie_meter             = $e->getDrieMeter();
        $object->vier_meter             = $e->getVierMeter();
        $object->elektra                = $e->getElektra();
        $object->promotieGeldenPerMeter = $e->getPromotieGeldenPerMeter();
        $object->promotieGeldenPerKraam = $e->getPromotieGeldenPerKraam();
        $object->afvaleiland            = $e->getAfvaleiland();
        $object->eenmaligElektra        = $e->getEenmaligElektra();


        return $object;
    }

    /**
     * @param Concreetplan[] $list
     * @return ConcreetplanModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e Concreetplan */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}