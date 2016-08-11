<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class MarktModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var string
     */
    public $afkorting;

    /**
     * @var string
     */
    public $naam;

    /**
     * @var string
     */
    public $geoArea;

    /**
     * @var string
     */
    public $soort;

    /**
     * @var string
     */
    public $marktDagen;

    /**
     * @var number
     */
    public $standaardKraamAfmeting;

    /**
     * @var boolean
     */
    public $extraMetersMogelijk;

    /**
     * @var multitype:string
     */
    public $aanwezigeOpties;

    /**
     * @var number
     */
    public $perfectViewNummer;
}