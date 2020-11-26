<?php
/*
 *  Copyright (C) 2020 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="AccountRepository")
 * @ORM\Table()
 */
class Account implements UserInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $naam;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $role;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $attempts;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastAttempt;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $locked;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $active;

    /**
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNaam()
    {
        return $this->naam;
    }

    /**
     * @param string $naam
     */
    public function setNaam($naam)
    {
        $this->naam = $naam;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getRoles()
    {
        return array($this->role);
    }

    public function eraseCredentials()
    {
        //
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->getRoles(),
            // see section on salt below
            //$this->salt,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $roles
            //$this->salt,
        ) = unserialize($serialized);
        $this->setRole(reset($roles));
    }

    /**
     * Set attempts
     *
     * @param integer $attempts
     *
     * @return Account
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;

        return $this;
    }

    /**
     * Get attempts
     *
     * @return integer
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * Set lastAttempt
     *
     * @param \DateTime $lastAttempt
     *
     * @return Account
     */
    public function setLastAttempt($lastAttempt)
    {
        $this->lastAttempt = $lastAttempt;

        return $this;
    }

    /**
     * Get lastAttempt
     *
     * @return \DateTime
     */
    public function getLastAttempt()
    {
        return $this->lastAttempt;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     *
     * @return Account
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    public function isActive()
    {
        return $this->getActive();
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getActive()
    {
        return $this->active;
    }
}
