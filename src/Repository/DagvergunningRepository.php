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

use App\Entity\Dagvergunning;
use App\Entity\Koopman;
use App\Entity\Markt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 */
class DagvergunningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dagvergunning::class);
    }

    /**
     * @param integer $id
     * @return Dagvergunning|NULL
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * @param array $q Key/Value pair with query arguments, supported keys: marktId, dag, koopmanId, erkenningsnummer, doorgehaald, dagRange
     * @param number $offset
     * @param number $size
     * @return \Doctrine\ORM\Tools\Pagination\Paginator|Dagvergunning[]
     */
    public function search($q, $offset = 0, $size = 10)
    {
        $qb = $this->createQueryBuilder('dvg');
        $qb->select('dvg');
        $qb->addSelect('mkt');
        $qb->addSelect('koopman');
        $qb->join('dvg.markt', 'mkt');
        $qb->leftJoin('dvg.koopman', 'koopman');
        $maandGeleden = new \DateTime();
        $maandGeleden->modify('-1 month');
        $maandGeleden->setTime(0, 0, 0);
        $qb->leftJoin('koopman.dagvergunningen', 'vergunningen', Join::WITH, sprintf('vergunningen.dag >= \'%s\'', $maandGeleden->format('Y-m-d')));
        $qb->leftJoin('vergunningen.vergunningControles', 'controles');

        // search
        if (isset($q['marktId']) === true && $q['marktId'] !== null && $q['marktId'] !== '') {
            $qb->andWhere('mkt = :marktId');
            $qb->setParameter('marktId', $q['marktId']);
        }
        if (isset($q['dag']) === true && $q['dag'] !== null && $q['dag'] !== '') {
            $qb->andWhere('dvg.dag = :dag');
            $qb->setParameter('dag', $q['dag']);
        }
        if (isset($q['dagRange']) === true && $q['dagRange'] !== null && count($q['dagRange']) !== 0) {
            $qb->andWhere('dvg.dag BETWEEN :dagStart AND :dagEind');
            $qb->setParameter('dagStart', $q['dagRange'][0]);
            $qb->setParameter('dagEind', $q['dagRange'][1]);
        }
        if (isset($q['koopmanId']) === true && $q['koopmanId'] !== null && $q['koopmanId'] !== '') {
            $qb->andWhere('koopman.id = :koopmanId');
            $qb->setParameter('koopmanId', $q['koopmanId']);
        }
        if (isset($q['erkenningsnummer']) === true && $q['erkenningsnummer'] !== null && $q['erkenningsnummer'] !== '') {
            $qb->andWhere('dvg.erkenningsnummerInvoerWaarde = :erkenningsnummer');
            $qb->setParameter('erkenningsnummer', $q['erkenningsnummer']);
        }
        if (isset($q['doorgehaald']) === false || $q['doorgehaald'] == '0') {
            $qb->andWhere('dvg.doorgehaald = :doorgehaald');
            $qb->setParameter('doorgehaald', false);
        }
        if (isset($q['doorgehaald']) === true && $q['doorgehaald'] == '1') {
            $qb->andWhere('dvg.doorgehaald = :doorgehaald');
            $qb->setParameter('doorgehaald', true);
        }
        if (isset($q['accountId']) === true) {
            $qb->leftJoin('dvg.registratieAccount', 'account');
            $qb->andWhere('account.id = :accountId');
            $qb->setParameter('accountId', $q['accountId']);
        }

        // sort
        $qb->addOrderBy('dvg.registratieDatumtijd', 'DESC');

        // pagination
        $qb->setMaxResults($size);
        $qb->setFirstResult($offset);

        // paginator
        $q = $qb->getQuery();
        return new Paginator($q);
    }

    public function findByMarktAndDag(Markt $markt, \DateTime $datum, $includeDoorgehaald = false)
    {
        $qb = $this->createQueryBuilder('dagvergunning');
        $qb->select('dagvergunning')->addSelect('koopman')->addSelect('markt');
        $qb->join('dagvergunning.markt', 'markt');
        $qb->leftJoin('dagvergunning.koopman', 'koopman');
        $qb->andWhere('dagvergunning.markt = :markt')->setParameter('markt', $markt);
        $qb->andWhere('dagvergunning.dag = :dag')->setParameter('dag', $datum, \Doctrine\DBAL\Types\Type::DATE);
        if ($includeDoorgehaald === false) {
            $qb->andWhere('dagvergunning.doorgehaald = :doorgehaald')->setParameter('doorgehaald', false);
        }

        return $qb->getQuery()->execute();
    }

    public function findByKoopman(Koopman $koopman, $includeDoorgehaald = false)
    {
        $qb = $this->createQueryBuilder('dagvergunning');
        $qb->select('dagvergunning')->addSelect('koopman')->addSelect('markt');
        $qb->join('dagvergunning.markt', 'markt');
        $qb->join('dagvergunning.koopman', 'koopman');
        $qb->andWhere('dagvergunning.koopman = :koopman')->setParameter('koopman', $koopman);
        if ($includeDoorgehaald === false) {
            $qb->andWhere('dagvergunning.doorgehaald = :doorgehaald')->setParameter('doorgehaald', false);
        }
        $qb->addOrderBy('dagvergunning.dag', 'DESC');
        $qb->addOrderBy('markt.naam', 'ASC');
        $qb->addOrderBy('dagvergunning.registratieDatumtijd', 'DESC');
        return $qb->getQuery()->execute();
    }

    /**
     * @param $marktId
     * @param $dagStart
     * @param $dagEind
     * @return array|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMarktFrequentieDag($marktId, $dagStart, $dagEind)
    {
        $connection = $this->getEntityManagerInterface()->getConnection();

        return $connection->fetchAll("
    SELECT
		date_part('week',d.dag) AS week_nummer,
		string_agg(to_char(d.dag, 'YYYY-MM-DD'), '|') AS dagen,
		count(d.id) AS aantal,
		k.id,
        k.erkenningsnummer,
		k.achternaam,
		k.voorletters
	FROM
		sollicitatie s
		LEFT JOIN dagvergunning d ON
			d.sollicitatie_id = s.id
			AND d.doorgehaald = false
			AND d.dag >= ?
			AND d.dag <= ?
		JOIN koopman k
			ON s.koopman_id = k.id
	WHERE
		s.doorgehaald = false
		AND s.markt_id = ?
		AND (s.status = 'vkk' OR s.status = 'vpl')
	GROUP BY
		k.id,
		week_nummer
	ORDER BY k.id, week_nummer ASC",
            [$dagStart, $dagEind, $marktId]);
    }

    /**
     * @param $marktId
     * @param $dagStart
     * @param $dagEind
     * @return array|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMarktFrequentieSollicitanten($marktId, $dagStart, $dagEind)
    {
        $connection = $this->getEntityManagerInterface()->getConnection();

        return $connection->fetchAll("
    SELECT
		date_part('week',d.dag) AS week_nummer,
		string_agg(to_char(d.dag, 'YYYY-MM-DD'), '|') AS dagen,
		count(d.id) AS aantal,
		k.id,
        k.erkenningsnummer,
		k.achternaam,
		k.voorletters
	FROM
		sollicitatie s
		LEFT JOIN dagvergunning d ON
			d.sollicitatie_id = s.id
			AND d.doorgehaald = false
			AND d.dag >= ?
			AND d.dag <= ?
		JOIN koopman k
			ON s.koopman_id = k.id
	WHERE
		s.doorgehaald = false
		AND s.markt_id = ?
		AND s.status = 'soll'
	GROUP BY
		k.id,
		week_nummer
	ORDER BY k.id, week_nummer ASC",
            [$dagStart, $dagEind, $marktId]);
    }

    /**
     * @param $marktId
     * @param $dagStart
     * @param $dagEind
     * @return array|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMarktPersoonlijkeAanwezigheid($marktId, $dagStart, $dagEind)
    {
        $connection = $this->getEntityManagerInterface()->getConnection();

        return $connection->fetchAll("
    SELECT
		k.id,
        k.erkenningsnummer,
		k.achternaam,
		k.voorletters,
		d.aanwezig,
		count(d.id) AS aantal,
		to_char(s.inschrijf_datum, 'YYYY-MM-DD') AS inschrijf_datum
	FROM
		sollicitatie s
		LEFT JOIN dagvergunning d ON
			d.sollicitatie_id = s.id
			AND d.doorgehaald = false
			AND d.dag >= ?
			AND d.dag <= ?
		JOIN koopman k
			ON s.koopman_id = k.id
	WHERE
		s.doorgehaald = false
		AND s.markt_id = ?
		AND d.aanwezig IS NOT NULL
	GROUP BY
		k.id,
		s.id,
		d.aanwezig
	ORDER BY k.id ASC",
            [$dagStart, $dagEind, $marktId]);
    }

    /**
     * @param $marktId
     * @param $dagStart
     * @param $dagEind
     * @return array|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getInvoer($marktId, $dagStart, $dagEind)
    {
        $connection = $this->getEntityManagerInterface()->getConnection();

        return $connection->fetchAll("
    SELECT
		k.id,
        k.erkenningsnummer,
		k.achternaam,
		k.voorletters,
		d.erkenningsnummer_invoer_methode,
		count(d.id) AS aantal
	FROM
		dagvergunning d
		JOIN koopman k
			ON d.koopman_id = k.id
	WHERE
		d.doorgehaald = false
        AND d.dag >= ?
        AND d.dag <= ?
		AND d.markt_id = ?
		AND d.aanwezig IS NOT NULL
	GROUP BY
		k.id,
		d.erkenningsnummer_invoer_methode
	ORDER BY k.id ASC",
            [$dagStart, $dagEind, $marktId]);
    }

    /**
     * @param Koopman $koopman
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     */
    public function getByKoopmanAndDates(Koopman $koopman, \DateTime $startDate, \DateTime $endDate)
    {
        $em = $this->getEntityManagerInterface();

        $dql = 'SELECT d
                FROM AppApiBundle:Dagvergunning d
                WHERE d.doorgehaald = false
                AND d.dag >= :startDate
                AND d.dag <= :endDate
                AND d.koopman = :koopman
                ';

        $query = $em->createQuery($dql)
            ->setParameters(array(
                'startDate' => $startDate,
                'endDate' => $endDate,
                'koopman' => $koopman,
            ));

        return $query->getResult();
    }

    /**
     * @param Markt $markt
     * @param \DateTime $dag
     * @return Dagvergunning[]
     */
    public function findForWeightCalculation(Markt $markt, \DateTime $dag)
    {
        $em = $this->getEntityManagerInterface();

        $maandGeleden = new \DateTime();
        $maandGeleden->modify('-1 month');
        $maandGeleden->setTime(0, 0, 0);

        $dql = 'SELECT d, k, v
                FROM AppApiBundle:Dagvergunning d
                JOIN d.koopman k
                LEFT JOIN k.dagvergunningen v WITH v.dag >= :maandGeleden
                WHERE d.doorgehaald = false
                AND d.markt = :markt
                AND d.dag = :dag
                ';

        $query = $em->createQuery($dql)
            ->setParameters(array(
                'maandGeleden' => $maandGeleden,
                'markt' => $markt,
                'dag' => $dag,
            ));

        return $query->getResult();
    }
}
