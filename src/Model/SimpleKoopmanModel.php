<?php
/*
 *  Copyright (c) 2020 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace App\Model;

use App\Entity\Vervanger;

class SimpleKoopmanModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var string
     */
    public $erkenningsnummer;

    /**
     * @var string
     */
    public $voorletters;

    /**
     * @var string
     */
    public $achternaam;

    /**
     * @var string
     */
    public $telefoon;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $fotoUrl;

    /**
     * @var string
     */
    public $fotoMediumUrl;

    /**
     * @var string
     */
    public $status;

    /**
     * @var Vervanger[]
     */
    public $vervangers;
}
