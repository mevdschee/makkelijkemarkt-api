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

class LineairplanModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var number
     */
    public $tariefPerMeter;

    /**
     * @var number
     */
    public $reinigingPerMeter;

    /**
     * @var number
     */
    public $toeslagBedrijfsafvalPerMeter;

    /**
     * @var number
     */
    public $toeslagKrachtstroomPerAansluiting;

    /**
     * @var boolean
     */
    public $gemeenschappelijkeReiniging;

    /**
     * @var boolean
     */
    public $gemeentelijkeBedrijfsafval;

    /**
     * @var number
     */
    public $promotieGeldenPerMeter;

    /**
     * @var number
     */
    public $promotieGeldenPerKraam;

    /**
     * @var number
     */
    public $eenmaligElektra;

    /**
     * @var number
     */
    public $elektra;
}
