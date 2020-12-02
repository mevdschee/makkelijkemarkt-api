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

use App\Entity\Tariefplan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TariefplanRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface $em
     */

    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tariefplan::class);
        $this->em = $registry->getManager();
    }

    /**
     * @param $markt
     * @param $dag
     * @return Tariefplan|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByMarktAndDag($markt, $dag)
    {
        $dql = 'SELECT t
                FROM App:Tariefplan t
                WHERE t.markt = :markt
                AND   t.geldigVanaf <= :dag
                AND   t.geldigTot   >= :dag';

        $query = $this->em->createQuery($dql)
            ->setParameters(array(
                'markt' => $markt,
                'dag' => $dag,
            ))
            ->setMaxResults(1);

        return $query->getOneOrNullResult();
    }
}
