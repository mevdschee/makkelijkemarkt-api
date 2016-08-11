<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper;


use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Lineairplan;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\LineairplanModel;

class LineairplanMapper
{
    /**
     * @param Lineairplan $e
     * @return LineairPlanModel
     */
    public function singleEntityToModel(Lineairplan $e)
    {
        $object                                    = new LineairplanModel();
        $object->id                                = $e->getId();
        $object->tariefPerMeter                    = $e->getTariefPerMeter();
        $object->reinigingPerMeter                 = $e->getReinigingPerMeter();
        $object->toeslagBedrijfsafvalPerMeter      = $e->getToeslagBedrijfsafvalPerMeter();
        $object->toeslagKrachtstroomPerAansluiting = $e->getToeslagKrachtstroomPerAansluiting();
        $object->promotieGeldenPerMeter            = $e->getPromotieGeldenPerMeter();
        $object->promotieGeldenPerKraam            = $e->getPromotieGeldenPerKraam();
        $object->afvaleiland                       = $e->getAfvaleiland();
        $object->eenmaligElektra                   = $e->getEenmaligElektra();

        return $object;
    }

    /**
     * @param Lineairplan[] $list
     * @return LineairplanModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e Lineairplan */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}