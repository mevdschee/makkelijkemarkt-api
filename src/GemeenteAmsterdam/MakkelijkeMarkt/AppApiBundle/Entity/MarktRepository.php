<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class MarktRepository extends EntityRepository
{
    /**
     * @param integer $id
     * @return Markt|NULL
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * @param integer $kaartnr
     * @return Markt|NULL
     */
    public function getByPerfectViewNummer($kaartnr)
    {
        return $this->findOneBy(['perfectViewNummer' => $kaartnr]);
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\ORM\EntityRepository::findAll()
     */
    public function findAll()
    {
        return $this->findBy([], ['naam' => 'ASC', 'afkorting' => 'ASC']);
    }
}