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
class MarktExtraDataRepository extends EntityRepository
{
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
