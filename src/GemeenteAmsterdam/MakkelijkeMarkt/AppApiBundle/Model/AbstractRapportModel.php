<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class AbstractRapportModel
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string date as yyyy-mm-dd hh:ii:ss
     */
    public $generationDate;

    /**
     * @var string[]
     */
    public $parameters;

    /**
     * @var mixed[]
     */
    public $result;
}