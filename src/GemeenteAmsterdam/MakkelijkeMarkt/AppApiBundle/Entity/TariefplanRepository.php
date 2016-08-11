<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TariefplanRepository extends EntityRepository
{

    /**
     * @param $markt
     * @param $dag
     * @return Tariefplan|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByMarktAndDag($markt, $dag) {
        $em = $this->getEntityManager();

        $dql = 'SELECT t
                FROM AppApiBundle:Tariefplan t
                WHERE t.markt = :markt
                AND   t.geldigVanaf <= :dag
                AND   t.geldigTot   >= :dag';


        $query = $em->createQuery($dql)
            ->setParameters(array(
                'markt' => $markt,
                'dag'   => $dag
            ))
            ->setMaxResults(1);

        return $query->getOneOrNullResult();
    }
}