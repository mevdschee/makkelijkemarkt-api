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
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 */
class KoopmanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Koopman::class);
    }

    /**
     * @param array $q Key/Value pair with query arguments, supported keys: freeSearch, voorletters, achternaam, telefoon, email, erkenningsnummer
     * @param number $offset
     * @param number $size
     * @return \Doctrine\ORM\Tools\Pagination\Paginator|Koopman[]
     */
    public function search($q, $offset = 0, $size = 10)
    {
        $qb = $this->createQueryBuilder('reg');
        $qb->select('reg');

        // search
        if (isset($q['freeSearch']) === true && $q['freeSearch'] !== null && $q['freeSearch'] !== '') {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(reg.voorletters)', 'LOWER(:freeSearch_voorletters)'),
                $qb->expr()->like('LOWER(reg.achternaam)', 'LOWER(:freeSearch_achternaam)'),
                $qb->expr()->like('reg.telefoon', ':freeSearch_telefoon'),
                $qb->expr()->like('reg.email', ':freeSearch_email'),
                $qb->expr()->like('reg.erkenningsnummer', ':freeSearch_erkenningsnummer')
            ));
            $qb->setParameter('freeSearch_voorletters', '%' . $q['freeSearch'] . '%');
            $qb->setParameter('freeSearch_achternaam', '%' . $q['freeSearch'] . '%');
            $qb->setParameter('freeSearch_telefoon', '%' . $q['freeSearch'] . '%');
            $qb->setParameter('freeSearch_email', '%' . $q['freeSearch'] . '%');
            $qb->setParameter('freeSearch_erkenningsnummer', '%' . $q['freeSearch'] . '%');
        }
        if (isset($q['voorletters']) === true && $q['voorletters'] !== null && $q['voorletters'] !== '') {
            $qb->andWhere('LOWER(reg.voorletters) LIKE LOWER(:voorletters)');
            $qb->setParameter('voorletters', '%' . $q['voorletters'] . '%');
        }
        if (isset($q['achternaam']) === true && $q['achternaam'] !== null && $q['achternaam'] !== '') {
            $qb->andWhere('LOWER(reg.achternaam) LIKE LOWER(:achternaam)');
            $qb->setParameter('achternaam', '%' . $q['achternaam'] . '%');
        }
        if (isset($q['telefoon']) === true && $q['telefoon'] !== null && $q['telefoon'] !== '') {
            $qb->andWhere('reg.telefoon LIKE :telefoon');
            $qb->setParameter('telefoon', '%' . $q['telefoon'] . '%');
        }
        if (isset($q['email']) === true && $q['email'] !== null && $q['email'] !== '') {
            $qb->andWhere('reg.email LIKE :email');
            $qb->setParameter('email', '%' . $q['email'] . '%');
        }
        if (isset($q['erkenningsnummer']) === true && $q['erkenningsnummer'] !== null && $q['erkenningsnummer'] !== '') {
            $qb->andWhere('reg.erkenningsnummer LIKE :erkenningsnummer');
            $qb->setParameter('erkenningsnummer', '%' . $q['erkenningsnummer'] . '%');
        }
        if (isset($q['status']) === true && $q['status'] !== null && $q['status'] !== '' && $q['status'] !== -1 && $q['status'] !== '-1') {
            $qb->andWhere('reg.status = :status');
            $qb->setParameter('status', $q['status']);
        }

        // sort
        $qb->addOrderBy('reg.achternaam', 'ASC');
        $qb->addOrderBy('reg.voorletters', 'ASC');
        $qb->addOrderBy('reg.erkenningsnummer', 'ASC');

        // pagination
        $qb->setMaxResults($size);
        $qb->setFirstResult($offset);

        // paginator
        $q = $qb->getQuery();
        return new Paginator($q);
    }

    /**
     * @param integer $id
     * @return Koopman|NULL
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * @param string $erkenningsnummer
     * @return Koopman|NULL
     */
    public function getByErkenningsnummer($erkenningsnummer)
    {
        return $this->findOneBy(['erkenningsnummer' => $erkenningsnummer]);
    }

    /**
     * @param integer $marktId
     * @param integer $sollicitatieNummer
     * @return NULL|Koopman
     */
    public function getBySollicitatienummer($marktId, $sollicitatieNummer)
    {
        $qb = $this->createQueryBuilder('koopman')
            ->addSelect('koopman')
            ->join('koopman.sollicitaties', 'sollicitatie')
            ->join('sollicitatie.markt', 'markt')
            ->andWhere('markt.id = :marktId')
            ->setParameter('marktId', $marktId)
            ->andWhere('sollicitatie.sollicitatieNummer = :sollicitatieNummer')
            ->setParameter('sollicitatieNummer', $sollicitatieNummer);
        $results = $qb->getQuery()->execute();
        if (count($results) === 0) {
            return null;
        }

        return reset($results);
    }

    /**
     * @param integer $kaartnr
     * @return Koopman|NULL
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
        return $this->findBy([], ['achternaam' => 'ASC', 'voorletters' => 'ASC', 'erkenningsnummer' => 'ASC']);
    }

    public function findWithNietZelfAanwezigInPeriode(array $koopmanIds, \DateTime $start, \DateTime $eind)
    {
        $qb = $this->createQueryBuilder('koopman');
        $qb->join('koopman.dagvergunning', 'dagvergunning');
        $qb->andWhere('dagvergunning.doorgehaald = :doorgehaald');
        $qb->setParameter('doorgehaald', false);
        $qb->andWhere('koopman.id IN (:koopmanIds)');
        $qb->setParameter('koopmanIds', $koopmanIds);
        $qb->andWhere('dagvergunning.dag BETWEEN :start AND :eind');
        $qb->setParameter('start', $start);
        $qb->setParameter('eind', $eind);
        $qb->leftJoin('dagvergunning.vergunningControles', 'controle');
        $qb->andWhere('(dagvergunning.aanwezig != :zelf1 OR controle.aanwezig != :zelf2)');
        $qb->setParameter('zelf1', 'zelf');
        $qb->setParameter('zelf2', 'zelf');
        return $qb->getQuery()->execute();
    }

    public function getWithDagvergunningOnDag(\DateTime $dag)
    {
        $qb = $this->createQueryBuilder('koopman');
        $qb->join('koopman.dagvergunningen', 'dagvergunning');
        $qb->andWhere('dagvergunning.doorgehaald = :doorgehaald');
        $qb->setParameter('doorgehaald', false);
        $qb->andWhere('dagvergunning.dag = :date');
        $qb->setParameter('date', $dag->format('Y-m-d'));
        return $qb->getQuery()->execute();
    }
}
