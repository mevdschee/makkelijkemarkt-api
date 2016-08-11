<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Enum;

class Roles
{
    const ROLE_USER  = 'Gebruiker';
    const ROLE_ADMIN = 'Beheerder';

    public static function all() {
        $object = new self();
        $reflection = new \ReflectionClass($object);
        return $reflection->getConstants();
    }
}
