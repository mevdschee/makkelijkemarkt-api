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

class MarktModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var string
     */
    public $afkorting;

    /**
     * @var string
     */
    public $naam;

    /**
     * @var string
     */
    public $geoArea;

    /**
     * @var string
     */
    public $soort;

    /**
     * @var string
     */
    public $marktDagen;

    /**
     * @var number
     */
    public $standaardKraamAfmeting;

    /**
     * @var boolean
     */
    public $extraMetersMogelijk;

    /**
     * @var string
     */
    public $aanwezigeOpties;

    /**
     * @var number
     */
    public $perfectViewNummer;

    /**
     * @var number
     */
    public $aantalKramen;

    /**
     * @var number
     */
    public $aantalMeter;

    /**
     * @var integer
     */
    public $auditMax;

    /**
     * @var bool
     */
    public $kiesJeKraamMededelingActief;

    /**
     * @var string
     */
    public $kiesJeKraamMededelingTitel;

    /**
     * @var string
     */
    public $kiesJeKraamMededelingTekst;

    /**
     * @var bool
     */
    public $kiesJeKraamActief;

    /**
     * @var string
     */
    public $kiesJeKraamFase;

    /**
     * @var string
     */
    public $kiesJeKraamGeblokkeerdePlaatsen;

    /**
     * @var array|string[]
     */
    public $kiesJeKraamGeblokkeerdeData;

    /**
     * @var string
     */
    public $kiesJeKraamEmailKramenzetter;

    /**
     * @var string
     */
    public $marktDagenTekst;

    /**
     * @var string
     */
    public $indelingsTijdstipTekst;

    /**
     * @var string
     */
    public $telefoonNummerContact;

    /**
     * @var bool
     */
    public $makkelijkeMarktActief;

    /**
     * @var string
     */
    public $indelingstype;

    /**
     * @var boolean
     */
    public $isABlijstIndeling;
}
