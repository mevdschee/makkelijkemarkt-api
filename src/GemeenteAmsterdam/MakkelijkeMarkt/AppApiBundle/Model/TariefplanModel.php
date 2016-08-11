<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model;

class TariefplanModel
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
     * @var string yyyy-mm-dd hh:ii:ss
     */
    public $geldigVanaf;

    /**
     * @var null|string yyyy-mm-dd hh:ii:ss
     */
    public $geldigTot;

    /**
     * @var LinearplanModel
     */
    public $lineairplan;

    /**
     * @var number
     */
    public $marktId;
}