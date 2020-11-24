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

namespace App\Repository;

use App\Entity\MarktExtraData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 */
class MarktExtraDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarktExtraData::class);
    }

    /**
     * @param number $kaartnr
     * @return MarktExtraData|NULL
     */
    public function getByPerfectViewNumber($kaartnr)
    {
        return $this->find(strtoupper($kaartnr));
    }

    public function getByAfkorting($afkorting)
    {
        return $this->find($afkorting);
    }
}
