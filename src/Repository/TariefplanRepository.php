<?php

namespace App\Repository;

use App\Entity\Tariefplan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TariefplanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tariefplan::class);
    }

    /**
     * @param $markt
     * @param $dag
     * @return Tariefplan|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByMarktAndDag($markt, $dag)
    {
        $em = $this->getEntityManagerInterface();

        $dql = 'SELECT t
                FROM AppApiBundle:Tariefplan t
                WHERE t.markt = :markt
                AND   t.geldigVanaf <= :dag
                AND   t.geldigTot   >= :dag';

        $query = $em->createQuery($dql)
            ->setParameters(array(
                'markt' => $markt,
                'dag' => $dag,
            ))
            ->setMaxResults(1);

        return $query->getOneOrNullResult();
    }
}
