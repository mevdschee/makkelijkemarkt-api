<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class SimpleSollicitatieModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var number
     */
    public $sollicitatieNummer;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string[]
     */
    public $vastePlaatsen;

    /**
     * @var number
     */
    public $aantal3MeterKramen;

    /**
     * @var number
     */
    public $aantal4MeterKramen;

    /**
     * @var number
     */
    public $aantalExtraMeters;

    /**
     * @var number
     */
    public $aantalElektra;

    /**
     * @var boolean
     */
    public $krachtstroom;

    /**
     * @var boolean
     */
    public $doorgehaald;

    /**
     * @var string
     */
    public $doorgehaaldReden;

    /**
     * @var SimpleMarktModel
     */
    public $markt;
}