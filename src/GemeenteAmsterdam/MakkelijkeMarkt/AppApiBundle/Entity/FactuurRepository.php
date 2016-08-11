<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\EntityRepository;

class FactuurRepository extends EntityRepository
{
    public function getFacturenByDateRange($van, $tot) {
        $em = $this->getEntityManager();

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

    public function getFacturenByDateRangeAndMarkt($markt, $van, $tot) {
        $em = $this->getEntityManager();

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
                'van'   => $van,
                'tot'   => $tot,
                'markt' => $markt
            ));

        return $query->getResult();
    }
}