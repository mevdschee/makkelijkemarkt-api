<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\MarktModel;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\SimpleMarktModel;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class MarktMapper
{
    /**
     * @param Markt $e
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\MarktModel
     */
    public function singleEntityToModel(Markt $e)
    {
        $object = new MarktModel();
        $object->id = $e->getId();
        $object->naam = $e->getNaam();
        $object->afkorting = $e->getAfkorting();
        $object->soort = $e->getSoort();
        $object->marktDagen = $e->getMarktDagen();
        $object->geoArea = $e->getGeoArea();
        $object->standaardKraamAfmeting = $e->getStandaardKraamAfmeting();
        $object->extraMetersMogelijk = $e->getExtraMetersMogelijk();
        $object->aanwezigeOpties = [];
        foreach ($e->getAanwezigeOpties() as $key)
            $object->aanwezigeOpties[$key] = true;
        $object->perfectViewNummer = $e->getPerfectViewNummer();
        return $object;
    }

    /**
     * @param Markt $e
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\SimpleMarktModel
     */
    public function singleEntityToSimpleModel(Markt $e)
    {
        $object = new SimpleMarktModel();
        $object->id = $e->getId();
        $object->naam = $e->getNaam();
        $object->afkorting = $e->getAfkorting();
        return $object;
    }

    /**
     * @param multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt $list
     * @return multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\MarktModel
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e Markt */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }

    /**
     * @param multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt $list
     * @return multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\SimpleMarktModel
     */
    public function multipleEntityToSimpleModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e Markt */
            $result[] = $this->singleEntityToSimpleModel($e);
        }
        return $result;
    }

}