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

namespace App\Model;

class TokenModel
{
    /**
     * @var string
     */
    public $uuid;

    /**
     * @var AccountModel
     */
    public $account;

    /**
     * @var string yyyy-mm-dd hh:ii:ss
     */
    public $creationDate;

    /**
     * @var number in seconds
     */
    public $lifeTime;

    /**
     * @var number in seconds
     */
    public $timeLeft;

    /**
     * @var string
     */
    public $deviceUuid;

    /**
     * @var string
     */
    public $clientApp;

    /**
     * @var string
     */
    public $clientVersion;
}
