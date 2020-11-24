<?php

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
