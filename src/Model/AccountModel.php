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

namespace App\Model;

class AccountModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $naam;

    /**
     * @var string
     */
    public $username;

    /**
     * @var array
     */
    public $roles;

    /**
     * @var boolean
     */
    public $locked;

    /**
     * @var boolean
     */
    public $active;
}
