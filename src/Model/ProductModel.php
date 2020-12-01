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

class ProductModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var string
     */
    public $naam;

    /**
     * @var string
     */
    public $bedrag;

    /**
     * @var string
     */
    public $aantal;

    /**
     * @var string
     */
    public $totaal;

    /**
     * @var string
     */
    public $btw_percentage;

    /**
     * @var string
     */
    public $btw_totaal;

    /**
     * @var string
     */
    public $totaal_inclusief;
}
