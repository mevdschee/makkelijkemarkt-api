<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class NotitieRepository extends EntityRepository
{
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
        if ($verwijderdStatus === self::VERWIJDERDSTATUS_ACTIVE || $verwijderdStatus === self::VERWIJDERDSTATUS_REMOVED)
            $qb->andWhere('notitie.verwijderd = :verwijderd')->setParameter('verwijderd', $verwijderdStatus);
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