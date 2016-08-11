<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class MarktExtraDataRepository extends EntityRepository
{
    /**
     * @param number $kaartnr
     * @return MarktExtraData|NULL
     */
    public function getByPerfectViewNumber($kaartnr)
    {
        return $this->find($kaartnr);
    }
}