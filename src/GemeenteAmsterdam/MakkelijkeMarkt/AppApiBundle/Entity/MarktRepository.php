<?php
/*
 *  Copyright (C) 2017 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
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
     * @param string $afkorting
     * @return Markt|NULL
     */
    public function getByAfkorting($afkorting)
    {
        return $this->findOneBy(['afkorting' => strtoupper($afkorting)]);
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