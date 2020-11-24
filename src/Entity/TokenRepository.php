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

namespace App\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Repository for Token entity
 */
class TokenRepository extends EntityRepository
{
    /**
     * @param string $uuid
     * @return Token|NULL
     */
    public function getByUuid($uuid)
    {
        return $this->find($uuid);
    }

    /**
     * @param Account $account
     * @param number $listOffset
     * @param number $listLength
     * @return Token[]|Paginator
     */
    public function search(Account $account, $listOffset, $listLength)
    {
        $qb = $this->createQueryBuilder('token');
        $qb->andWhere('token.account = :account');
        $qb->setParameter('account', $account);
        $qb->addOrderBy('token.creationDate', 'DESC');
        $qb->addOrderBy('token.uuid', 'ASC');
        $qb->setFirstResult($listOffset);
        $qb->setMaxResults($listLength);

        $query = $qb->getQuery();

        return new Paginator($query);
    }
}
