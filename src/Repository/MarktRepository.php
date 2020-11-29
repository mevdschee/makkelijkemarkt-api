<?php
/*
 *  Copyright (c) 2020 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace App\Repository;

use App\Entity\Markt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 */
class MarktRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Markt::class);
    }

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
        return $this->findOneBy(['afkorting' => $afkorting]);
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
