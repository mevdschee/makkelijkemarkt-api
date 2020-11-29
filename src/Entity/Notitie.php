<?php
/*
 *  Copyright (c) 2020 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotitieRepository")
 * @ORM\Table()
 */
class Notitie
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
     * @var Markt
     *
     * @ORM\ManyToOne(targetEntity="Markt", fetch="LAZY")
     * @ORM\JoinColumn(name="markt_id", referencedColumnName="id", nullable=false)
     */
    private $markt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=false)
     */
    private $dag;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $bericht;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $afgevinktStatus;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $verwijderd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $aangemaaktDatumtijd;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $aangemaaktGeolocatieLat;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $aangemaaktGeolocatieLong;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $afgevinktDatumtijd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $verwijderdDatumtijd;

    /**
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \App\Entity\Markt
     */
    public function getMarkt()
    {
        return $this->markt;
    }

    /**
     * @param Markt $markt
     */
    public function setMarkt(Markt $markt)
    {
        $this->markt = $markt;
    }

    /**
     * @return \DateTime
     */
    public function getDag()
    {
        return $this->dag;
    }

    /**
     * @param \DateTime $dag
     */
    public function setDag(\DateTime $dag)
    {
        $this->dag = $dag;
    }

    /**
     * @return string
     */
    public function getBericht()
    {
        return $this->bericht;
    }

    /**
     * @param string $bericht
     */
    public function setBericht($bericht)
    {
        $this->bericht = $bericht;
    }

    /**
     * @return boolean
     */
    public function getAfgevinktStatus()
    {
        return $this->afgevinktStatus;
    }

    /**
     * @param boolean $afgevinktStatus
     */
    public function setAfgevinktStatus($afgevinktStatus)
    {
        $this->afgevinktStatus = $afgevinktStatus;
    }

    /**
     * @return boolean
     */
    public function getVerwijderd()
    {
        return $this->verwijderd;
    }

    /**
     * @param boolean $verwijderd
     */
    public function setVerwijderd($verwijderd)
    {
        $this->verwijderd = $verwijderd;
    }

    /**
     * @return \DateTime
     */
    public function getAangemaaktDatumtijd()
    {
        return $this->aangemaaktDatumtijd;
    }

    /**
     * @param \DateTime $aangemaaktDatumtijd
     */
    public function setAangemaaktDatumtijd(\DateTime $aangemaaktDatumtijd)
    {
        $this->aangemaaktDatumtijd = $aangemaaktDatumtijd;
    }

    /**
     * @return number
     */
    public function getAangemaaktGeolocatie()
    {
        return [$this->aangemaaktGeolocatieLat, $this->aangemaaktGeolocatieLong];
    }

    /**
     * @param float $lat
     * @param float $long
     */
    public function setAangemaaktGeolocatie($lat, $long)
    {
        $this->aangemaaktGeolocatieLat = $lat;
        $this->aangemaaktGeolocatieLong = $long;
    }

    /**
     * @return \DateTime
     */
    public function getAfgevinktDatumtijd()
    {
        return $this->afgevinktDatumtijd;
    }

    /**
     * @param \DateTime $afgevinktDatumtijd
     */
    public function setAfgevinktDatumtijd(\DateTime $afgevinktDatumtijd = null)
    {
        $this->afgevinktDatumtijd = $afgevinktDatumtijd;
    }

    /**
     * @return \DateTime
     */
    public function getVerwijderdDatumtijd()
    {
        return $this->verwijderdDatumtijd;
    }

    /**
     * @param \DateTime $verwijderdDatumtijd
     */
    public function setVerwijderdDatumtijd(\DateTime $verwijderdDatumtijd)
    {
        $this->verwijderdDatumtijd = $verwijderdDatumtijd;
    }
}
