<?php

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
