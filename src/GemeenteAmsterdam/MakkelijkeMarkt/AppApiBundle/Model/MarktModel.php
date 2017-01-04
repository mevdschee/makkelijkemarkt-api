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
     * @var multitype:string
     */
    public $aanwezigeOpties;

    /**
     * @var number
     */
    public $perfectViewNummer;
}