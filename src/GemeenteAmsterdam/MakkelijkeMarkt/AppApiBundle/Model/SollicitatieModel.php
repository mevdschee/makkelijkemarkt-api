<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class SollicitatieModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var SimpleKoopmanModel
     */
    public $koopman;

    /**
     * @var SimpleMarktModel
     */
    public $markt;

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
     * @var number
     */
    public $aantalAfvaleilanden;

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
}