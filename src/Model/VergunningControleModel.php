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

class VergunningControleModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var string
     */
    public $aanwezig;

    /**
     * @var string Date as yyyy-mm-dd hh:ii:ss
     */
    public $registratieDatumtijd;

    /**
     * @var array Geo location (lat, long)
     */
    public $registratieGeolocatie;

    /**
     * @var AccountModel
     */
    public $registratieAccount;

    /**
     * @var int
     */
    public $ronde;
}
