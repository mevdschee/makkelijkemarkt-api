<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class TokenModel
{
    /**
     * @var string
     */
    public $uuid;

    /**
     * @var AccountModel
     */
    public $account;

    /**
     * @var string yyyy-mm-dd hh:ii:ss
     */
    public $creationDate;

    /**
     * @var number in seconds
     */
    public $lifeTime;

    /**
     * @var number in seconds
     */
    public $timeLeft;

    /**
     * @var string
     */
    public $deviceUuid;

    /**
     * @var string
     */
    public $clientApp;

    /**
     * @var string
     */
    public $clientVersion;
}