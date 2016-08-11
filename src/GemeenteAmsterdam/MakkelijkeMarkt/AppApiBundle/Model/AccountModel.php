<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class AccountModel
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $naam;

    /**
     * @var string
     */
    public $username;

    /**
     * @var array
     */
    public $roles;

    /**
     * @var boolean
     */
    public $locked;
}