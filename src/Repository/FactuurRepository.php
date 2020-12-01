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

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FactuurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Factuur::class);
    }

    public function getFacturenByDateRange($van, $tot)
    {
        $em = $this->getEntityManagerInterface();

        $dql = 'SELECT f
                FROM AppApiBundle:Factuur f
                JOIN f.dagvergunning d
                WHERE d.verwijderdDatumtijd is null
                AND d.doorgehaald = false
                AND d.dag >= :van
                AND d.dag <= :tot
                ';

        $query = $em->createQuery($dql)
            ->setParameters(array(
                'van' => $van,
                'tot' => $tot,
            ));

        return $query->getResult();
    }

    public function getFacturenByDateRangeAndMarkt($markt, $van, $tot)
    {
        $em = $this->getEntityManagerInterface();

        $dql = 'SELECT f
                FROM AppApiBundle:Factuur f
                JOIN f.dagvergunning d
                WHERE d.verwijderdDatumtijd is null
                AND d.doorgehaald = false
                AND d.dag >= :van
                AND d.dag <= :tot
                AND d.markt = :markt
                ';

        $query = $em->createQuery($dql)
            ->setParameters(array(
                'van' => $van,
                'tot' => $tot,
                'markt' => $markt,
            ));

        return $query->getResult();
    }
}
