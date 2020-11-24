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

use App\Entity\Koopman;
use App\Entity\Markt;
use App\Entity\Sollicitatie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 */
class SollicitatieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sollicitatie::class);
    }

    /**
     * @param integer $id
     * @return Sollicitatie|NULL
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * @param integer $kaartnr
     * @return Sollicitatie|NULL
     */
    public function getByPerfectViewNummer($kaartnr)
    {
        return $this->findOneBy(['perfectViewNummer' => $kaartnr]);
    }

    /**
     * @param Markt $markt
     * @param number $offset
     * @param number $size
     * @param bool $includeDoorgehaald
     * @return \Doctrine\ORM\Tools\Pagination\Paginator|Sollicitatie[]
     */
    public function findByMarkt(Markt $markt, $offset = 0, $size = 10, $includeDoorgehaald = true)
    {
        $qb = $this
            ->createQueryBuilder('sollicitatie')
            ->select('sollicitatie')
            ->addSelect('koopman')
            ->addSelect('vervanger')
            ->addSelect('vervangerKoopman')
            ->join('sollicitatie.koopman', 'koopman')
            ->leftJoin('koopman.vervangersVan', 'vervanger')
            ->leftJoin('vervanger.vervanger', 'vervangerKoopman')
            ->andWhere('sollicitatie.markt = :markt')
            ->setParameter('markt', $markt)
            ->addOrderBy('sollicitatie.sollicitatieNummer', 'ASC');

        if ($includeDoorgehaald === false) {
            $qb->andWhere('koopman.status <> :notStatus');
            $qb->setParameter('notStatus', Koopman::STATUS_VERWIJDERD);
            $qb->andWhere('sollicitatie.doorgehaald = :doorgehaald');
            $qb->setParameter('doorgehaald', false);
        }

        // pagination
        $qb->setMaxResults($size);
        $qb->setFirstResult($offset);

        // paginator
        $q = $qb->getQuery();
        return new Paginator($q);
    }

    /**
     * @param Markt $markt
     * @param number $sollicitatieNummer
     * @return NULL|Sollicitatie
     */
    public function getByMarktAndSollicitatieNummer(Markt $markt, $sollicitatieNummer)
    {
        $qb = $this
            ->createQueryBuilder('sollicitatie')
            ->select('sollicitatie')
            ->addSelect('koopman')
            ->join('sollicitatie.koopman', 'koopman')
            ->andWhere('sollicitatie.markt = :markt')
            ->setParameter('markt', $markt)
            ->andWhere('sollicitatie.sollicitatieNummer = :sollicitatieNummer')
            ->setParameter('sollicitatieNummer', $sollicitatieNummer);

        $q = $qb->getQuery();
        $records = $q->execute();

        if (count($records) === 0) {
            return null;
        }

        return reset($records);
    }

    /**
     * @param Markt $markt
     * @param number $erkenningsNummer
     * @param boolean $doorgehaald
     * @return NULL|Sollicitatie
     */
    public function getByMarktAndErkenningsNummer(Markt $markt, $erkenningsNummer, $doorgehaald)
    {
        $qb = $this
            ->createQueryBuilder('sollicitatie')
            ->select('sollicitatie')
            ->addSelect('koopman')
            ->join('sollicitatie.koopman', 'koopman')
            ->andWhere('sollicitatie.markt = :markt')
            ->setParameter('markt', $markt)
            ->andWhere('koopman.erkenningsnummer = :erkenningsnummer')
            ->setParameter('erkenningsnummer', $erkenningsNummer)
            ->andWhere('sollicitatie.doorgehaald = :doorgehaald')
            ->setParameter('doorgehaald', $doorgehaald)
        ;

        $q = $qb->getQuery();
        $records = $q->execute();

        if (count($records) === 0) {
            return null;
        }

        return reset($records);
    }

    /**
     * @param Markt $markt
     * @param array $types
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return Sollicitatie[]
     */
    public function findByMarktInPeriod($markt, $types = array(), $startDate, $endDate)
    {
        $em = $this->getEntityManagerInterface();

        $dql = 'SELECT DISTINCT s
                FROM AppApiBundle:Sollicitatie s
                JOIN s.koopman k
                JOIN k.dagvergunningen d
                WITH s = d.sollicitatie
                WHERE d.markt = :markt
                AND d.doorgehaald = false
                AND s.doorgehaald = false';

        $parameters = array('markt' => $markt);

        if (count($types)) {
            $dql .= ' AND s.status IN (:types)';
            $parameters['types'] = $types;
        }

        if (null !== $startDate) {
            $dql .= ' AND d.dag >= :startdate and d.dag <= :enddate';
            $parameters['startdate'] = $startDate;
            $parameters['enddate'] = $endDate;
        }

        $dql .= ' ORDER BY s.sollicitatieNummer';

        $query = $em->createQuery($dql)
            ->setParameters($parameters);

        $sollicitaties = $query->getResult();

        return $sollicitaties;
    }
}
