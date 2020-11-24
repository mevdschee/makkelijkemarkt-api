<?php

namespace App\Model;

class FactuurModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var ProductModel[]
     */
    public $producten;

    /**
     * @var number
     */
    public $totaal;

    /**
     * @var number
     */
    public $exclusief;
}
