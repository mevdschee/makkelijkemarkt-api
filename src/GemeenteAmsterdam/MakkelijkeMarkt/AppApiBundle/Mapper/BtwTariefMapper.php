<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\BtwTarief;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\BtwTariefModel;

class BtwTariefMapper
{
    /**
     * @param BtwTarief $e
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\BtwTariefModel
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
     * @param multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\BtwTarief[] $list
     * @return multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\BtwTariefModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e BtwTarief */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}