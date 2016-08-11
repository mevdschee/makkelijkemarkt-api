<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class KoopmanModel
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
     * @var number
     */
    public $perfectViewNummer;

    /**
     * @var string
     */
    public $pasUid;

    /**
     * @var Sollicitatie[]
     */
    public $sollicitaties = [];

    /**
     * @var Vervanger[]
     */
    public $vervangers;
}