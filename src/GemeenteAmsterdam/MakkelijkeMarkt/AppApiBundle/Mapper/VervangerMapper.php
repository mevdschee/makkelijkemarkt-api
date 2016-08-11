<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\KoopmanModel;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\SimpleKoopmanModel;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\VervangerModel;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;


class VervangerMapper
{
    /**
     * @param Vervanger $e
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\KoopmanModel
     */
    public function singleEntityToModel(Vervanger $e)
    {
        $object = new VervangerModel();
        $object->pas_uid = $e->getPasUid();
        $object->vervanger_id = $e->getVervanger()->getId();
        return $object;
    }

    /**
     * @param multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger $list
     * @return multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\VervangerModel
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e Koopman */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}