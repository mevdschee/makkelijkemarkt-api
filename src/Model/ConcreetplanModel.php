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

class ConcreetplanModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var number
     */
    public $een_meter;

    /**
     * @var number
     */
    public $drie_meter;

    /**
     * @var number
     */
    public $vier_meter;

    /**
     * @var number
     */
    public $afvaleiland;

    /**
     * @var number
     */
    public $elektra;

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
}
