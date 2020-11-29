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

class AbstractRapportModel
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string date as yyyy-mm-dd hh:ii:ss
     */
    public $generationDate;

    /**
     * @var string[]
     */
    public $parameters;

    /**
     * @var mixed[]
     */
    public $result;
}
