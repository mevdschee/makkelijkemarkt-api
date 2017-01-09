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

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 */
class AccountRepository extends EntityRepository
{
    /**
     * @param integer $id
     * @return Account|NULL
     */
    public function getById($id)
    {
        return $this->find(intval($id));
    }

    public function getByUsername($username)
    {
        return $this->findOneBy(['username' => $username]);
    }

   /**
     * @param array $q Key/Value pair with query arguments, supported keys: naam
     * @param number $offset
     * @param number $size
     * @return \Doctrine\ORM\Tools\Pagination\Paginator|Account[]
     */
    public function search($q, $offset = 0, $size = 10)
    {
        $qb = $this->createQueryBuilder('account');
        $qb->select('account');

        // search
        if (isset($q['naam']) === true && $q['naam'] !== null && $q['naam'] !== '')
        {
            $qb->andWhere('LOWER(account.naam) LIKE LOWER(:naam)');
            $qb->setParameter('naam', '%' . $q['naam'] . '%');
        }

        // sort
        $qb->addOrderBy('account.naam', 'ASC');

        // pagination
        $qb->setMaxResults($size);
        $qb->setFirstResult($offset);

        // paginator
        $q = $qb->getQuery();
        return new Paginator($q);
    }
}