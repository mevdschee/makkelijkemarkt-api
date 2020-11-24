<?php
/*
 *  Copyright (C) 2020 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace App\Repository;

use App\Entity\Markt;
use App\Entity\Notitie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 */
class NotitieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notitie::class);
    }

    const VERWIJDERDSTATUS_ALL = -1;
    const VERWIJDERDSTATUS_ACTIVE = 0;
    const VERWIJDERDSTATUS_REMOVED = 1;

    /**
     * @param integer $id
     * @return Notitie|NULL
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * @param Markt $markt
     * @param number $offset
     * @param number $size
     * @return \Doctrine\ORM\Tools\Pagination\Paginator|Notitie[]
     */
    public function findByMarkt(Markt $markt, $offset = 0, $size = 10)
    {
        $qb = $this->createQueryBuilder('notitie')
            ->select('notitie')
            ->andWhere('notitie.markt = :markt')
            ->setParameter('markt', $markt)
            ->addOrderBy('notitie.aangemaaktDatumtijd', 'DESC')
            ->setMaxResults($size)
            ->setFirstResult($offset);
        return new Paginator($qb->getQuery());
    }

    /**
     * @param Markt $markt
     * @param string $dag
     * @param number $offset
     * @param number $size
     * @return \Doctrine\ORM\Tools\Pagination\Paginator|Notitie[]
     */
    public function findByMarktAndDag(Markt $markt, $dag, $verwijderdStatus = 0, $offset = 0, $size = 10)
    {
        $qb = $this->createQueryBuilder('notitie')
            ->select('notitie')
            ->andWhere('notitie.markt = :markt')
            ->setParameter('markt', $markt)
            ->andWhere('notitie.dag = :dag')
            ->setParameter('dag', $dag)
            ->addOrderBy('notitie.aangemaaktDatumtijd', 'DESC')
            ->setMaxResults($size)
            ->setFirstResult($offset);
        if ($verwijderdStatus === self::VERWIJDERDSTATUS_ACTIVE || $verwijderdStatus === self::VERWIJDERDSTATUS_REMOVED) {
            $qb->andWhere('notitie.verwijderd = :verwijderd')->setParameter('verwijderd', $verwijderdStatus);
        }

        return new Paginator($qb->getQuery());
    }

    /**
     * @param string $dag
     * @param number $offset
     * @param number $size
     * @return \Doctrine\ORM\Tools\Pagination\Paginator|Notitie[]
     */
    public function findByDag($dag, $offset = 0, $size = 10)
    {
        $qb = $this->createQueryBuilder('notitie')
            ->select('notitie')
            ->addSelect('markt')
            ->join('notitie.markt', 'markt')
            ->andWhere('notitie.dag = :dag')
            ->setParameter('dag', $dag)
            ->addOrderBy('markt.naam', 'ASC')
            ->addOrderBy('notitie.aangemaaktDatumtijd', 'DESC')
            ->setMaxResults($size)
            ->setFirstResult($offset);
        return new Paginator($qb->getQuery());
    }
}
