<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class SollicitatieRepository extends EntityRepository
{
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
     * @return \Doctrine\ORM\Tools\Pagination\Paginator|Sollicitatie[]
     */
    public function findByMarkt(Markt $markt, $offset = 0, $size = 10)
    {
        $qb = $this
            ->createQueryBuilder('sollicitatie')
            ->select('sollicitatie')
            ->addSelect('koopman')
            ->join('sollicitatie.koopman', 'koopman')
            ->andWhere('sollicitatie.markt = :markt')
            ->setParameter('markt', $markt)
            ->addOrderBy('sollicitatie.sollicitatieNummer', 'ASC');

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

        if (count($records) === 0)
            return null;

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

        if (count($records) === 0)
            return null;

        return reset($records);
    }

    /**
     * @param Markt $markt
     * @param array $types
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return Sollicitatie[]
     */
    public function findByMarktInPeriod($markt, $types = array(), $startDate, $endDate) {
        $em = $this->getEntityManager();

        $dql = 'SELECT DISTINCT s
                FROM AppApiBundle:Sollicitatie s
                JOIN s.koopman k
                JOIN k.dagvergunningen d
                WITH s = d.sollicitatie
                WHERE d.markt = :markt
                AND d.doorgehaald = false';

        $parameters = array('markt' => $markt);

        if (count($types)) {
            $dql .= ' AND s.status IN (:types)';
            $parameters['types'] = $types;
        }

        if (null !== $startDate) {
            $dql .= ' AND d.dag >= :startdate and d.dag <= :enddate';
            $parameters['startdate'] = $startDate;
            $parameters['enddate']   = $endDate;
        }

        $dql .= ' ORDER BY s.sollicitatieNummer';

        $query = $em->createQuery($dql)
            ->setParameters($parameters);

        $sollicitaties = $query->getResult();

        return $sollicitaties;
    }
}