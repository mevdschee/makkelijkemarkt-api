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

class SollicitatieModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var SimpleKoopmanModel
     */
    public $koopman;

    /**
     * @var SimpleMarktModel
     */
    public $markt;

    /**
     * @var number
     */
    public $sollicitatieNummer;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string[]
     */
    public $vastePlaatsen;

    /**
     * @var number
     */
    public $aantal3MeterKramen;

    /**
     * @var number
     */
    public $aantal4MeterKramen;

    /**
     * @var number
     */
    public $aantalExtraMeters;

    /**
     * @var number
     */
    public $aantalElektra;

    /**
     * @var number
     */
    public $aantalAfvaleilanden;

    /**
     * @var boolean
     */
    public $krachtstroom;

    /**
     * @var boolean
     */
    public $doorgehaald;

    /**
     * @var string
     */
    public $doorgehaaldReden;
}
