<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class SimpleKoopmanModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var string
     */
    public $erkenningsnummer;

    /**
     * @var string
     */
    public $voorletters;

    /**
     * @var string
     */
    public $achternaam;

    /**
     * @var string
     */
    public $telefoon;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $fotoUrl;

    /**
     * @var string
     */
    public $fotoMediumUrl;

    /**
     * @var string
     */
    public $status;

    /**
     * @var Vervanger[]
     */
    public $vervangers;
}