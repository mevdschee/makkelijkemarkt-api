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

class NotitieModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var SimpleMarktModel
     */
    public $markt;

    /**
     * @var string Date as yyyy-mm-dd
     */
    public $dag;

    /**
     * @var string
     */
    public $bericht;

    /**
     * @var boolean
     */
    public $afgevinktStatus;

    /**
     * @var boolean
     */
    public $verwijderdStatus;

    /**
     * @var string Date as yyyy-mm-dd hh:ii:ss
     */
    public $aangemaaktDatumtijd;

    /**
     * @var string Date as yyyy-mm-dd hh:ii:ss or NULL
     */
    public $afgevinktDatumtijd;

    /**
     * @var string Date as yyyy-mm-dd hh:ii:ss or NULL
     */
    public $verwijderdDatumtijd;

    /**
     * @var array Geo location (lat, long)
     */
    public $aangemaaktGeolocatie;
}