<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class NotitieModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var SimpleMarktModel
     */
    public $markt;

    /**
     * @var string Date as yyyy-mm-dd
     */
    public $dag;

    /**
     * @var string
     */
    public $bericht;

    /**
     * @var boolean
     */
    public $afgevinktStatus;

    /**
     * @var boolean
     */
    public $verwijderdStatus;

    /**
     * @var string Date as yyyy-mm-dd hh:ii:ss
     */
    public $aangemaaktDatumtijd;

    /**
     * @var string Date as yyyy-mm-dd hh:ii:ss or NULL
     */
    public $afgevinktDatumtijd;

    /**
     * @var string Date as yyyy-mm-dd hh:ii:ss or NULL
     */
    public $verwijderdDatumtijd;

    /**
     * @var array Geo location (lat, long)
     */
    public $aangemaaktGeolocatie;
}