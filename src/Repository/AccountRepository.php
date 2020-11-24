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

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

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
     * @param array $q Key/Value pair with query arguments, supported keys: naam, active, locked
     * @param number $offset
     * @param number $size
     * @return \Doctrine\ORM\Tools\Pagination\Paginator|Account[]
     */
    public function search($q, $offset = 0, $size = 10)
    {
        $qb = $this->createQueryBuilder('account');
        $qb->select('account');

        // search
        if (isset($q['naam']) === true && $q['naam'] !== null && $q['naam'] !== '') {
            $qb->andWhere('LOWER(account.naam) LIKE LOWER(:naam)');
            $qb->setParameter('naam', '%' . $q['naam'] . '%');
        }
        if (isset($q['active']) === true && $q['active'] !== null && $q['active'] !== '') {
            $qb->andWhere('account.active = :active');
            $qb->setParameter('active', $q['active']);
        }
        if (isset($q['locked']) === true && $q['locked'] !== null && $q['locked'] !== '') {
            $qb->andWhere('account.locked = :locked');
            $qb->setParameter('locked', $q['locked']);
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
