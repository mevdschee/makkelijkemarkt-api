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

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Token;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\TokenModel;

class TokenMapper
{
    /**
     * @var AccountMapper
     */
    protected $mapperAccount;

    /**
     * @param AccountMapper $mapperAccount
     */
    public function setAccountMapper(AccountMapper $mapperAccount)
    {
        $this->mapperAccount = $mapperAccount;
    }

    /**
     * @param Token $e
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\TokenModel
     */
    public function singleEntityToModel(Token $e)
    {
        $object = new TokenModel();
        $object->clientApp = $e->getClientApp();
        $object->clientVersion = $e->getClientVersion();
        $object->creationDate = $e->getCreationDate()->format('Y-m-d H:i:s');
        $object->deviceUuid = $e->getDeviceUuid();
        $object->lifeTime = $e->getLifeTime();
        $object->uuid = $e->getUuid();
        $object->timeLeft = $e->getCreationDate()->getTimestamp() + $e->getLifeTime() - time();
        $object->account = $this->mapperAccount->singleEntityToModel($e->getAccount());
        return $object;
    }

    /**
     * @param multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Token[] $list
     * @return multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\TokenModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e Token */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}