<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class TokenRepository extends EntityRepository
{
    /**
     * @param string $uuid
     * @return Token|NULL
     */
    public function getByUuid($uuid)
    {
        return $this->find($uuid);
    }
}