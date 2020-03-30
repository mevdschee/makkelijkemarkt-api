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

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger;

class KoopmanModel
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
    public $handhavingsVerzoek;

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
     * @var number
     */
    public $perfectViewNummer;

    /**
     * @var float
     */
    public $weging;

    /**
     * @var string
     */
    public $pasUid;

    /**
     * @var Sollicitatie[]
     */
    public $sollicitaties = [];

    /**
     * @var Vervanger[]
     */
    public $vervangers;
}