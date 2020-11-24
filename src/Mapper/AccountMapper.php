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

namespace App\Mapper;

use App\Entity\Account;
use App\Model\AccountModel;

class AccountMapper
{
    /**
     * @param Account $e
     * @return \App\Model\AccountModel
     */
    public function singleEntityToModel(Account $e)
    {
        $object = new AccountModel();
        $object->id = $e->getId();
        $object->naam = $e->getNaam();
        $object->email = $e->getEmail();
        $object->username = $e->getUsername();
        $object->roles = $e->getRoles();
        $object->locked = $e->getLocked();
        $object->active = $e->getActive();
        return $object;
    }

    /**
     * @param \App\Entity\Account[] $list
     * @return \App\Model\AccountModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e) {
            /* @var $e Account */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}
