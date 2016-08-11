<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Tariefplan;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\TariefplanModel;


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
        $object                 = new TariefplanModel();
        $object->id             = $e->getId();
        $object->naam           = $e->getNaam();
        $object->geldigVanaf    = $e->getGeldigVanaf();
        $object->geldigTot      = $e->getGeldigTot();
        $markt = $e->getMarkt();
        $object->marktId        = $markt->getId();
        $lineairPlan            = $e->getLineairplan();
        $object->lineairplan    = null === $lineairPlan ? null : $this->lineairplanMapper->singleEntityToModel($lineairPlan);
        $concreetPlan           = $e->getConcreetplan();
        $object->concreetplan   = null === $concreetPlan ? null : $this->concreetplanMapper->singleEntityToModel($concreetPlan);
        return $object;
    }

    /**
     * @param Tariefplan[] $list
     * @return TariefplanModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e Tariefplan */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}