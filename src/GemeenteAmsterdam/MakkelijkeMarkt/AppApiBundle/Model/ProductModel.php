<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model;

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