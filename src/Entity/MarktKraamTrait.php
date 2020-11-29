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

trait MarktKraamTrait
{
    /**
     * @var Koopman
     *
     * @ORM\ManyToOne(targetEntity="Koopman", fetch="EAGER")
     * @ORM\JoinColumn(name="vervanger_id", referencedColumnName="id", nullable=true)
     */
    protected $vervanger;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    protected $erkenningsnummerInvoerMethode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    protected $erkenningsnummerInvoerWaarde;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    protected $aanwezig;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $registratieDatumtijd;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    protected $registratieGeolocatieLat;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    protected $registratieGeolocatieLong;

    /**
     * @var Account
     * @ORM\ManyToOne(targetEntity="Account", fetch="LAZY")
     * @ORM\JoinColumn(name="registratie_account", referencedColumnName="id", nullable=true)
     */
    protected $registratieAccount;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $aantal3MeterKramen;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $aantal4MeterKramen;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $extraMeters;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $aantalElektra;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $afvaleiland;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $eenmalig_elektra;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $afvaleilandVast;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $krachtstroom;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $reiniging;

    /**
     * @var Sollicitatie
     * @ORM\ManyToOne(targetEntity="Sollicitatie", fetch="LAZY")
     * @ORM\JoinColumn(name="sollicitatie_id", referencedColumnName="id", nullable=true)
     */
    protected $sollicitatie;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $aantal3meterKramenVast;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $aantal4meterKramenVast;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $aantalExtraMetersVast;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $aantalElektraVast;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $krachtstroomVast;

    /**
     * @var string
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    protected $statusSolliciatie;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $notitie;

    /**
     * @param float $lat
     * @param float $long
     */
    public function setRegistratieGeolocatie($lat, $long)
    {
        $this->registratieGeolocatieLat = $lat;
        $this->registratieGeolocatieLong = $long;
    }

    /**
     * @return float[]
     */
    public function getRegistratieGeolocatie()
    {
        return [$this->registratieGeolocatieLat, $this->registratieGeolocatieLong];
    }

    /**
     * @return Koopman
     */
    public function getVervanger()
    {
        return $this->vervanger;
    }

    /**
     * @param Koopman $vervanger
     *
     */
    public function setVervanger($vervanger)
    {
        $this->vervanger = $vervanger;

        return $this;
    }

    /**
     * @return string
     */
    public function getErkenningsnummerInvoerMethode()
    {
        return $this->erkenningsnummerInvoerMethode;
    }

    /**
     * @param string $erkenningsnummerInvoerMethode
     *
     */
    public function setErkenningsnummerInvoerMethode($erkenningsnummerInvoerMethode)
    {
        $this->erkenningsnummerInvoerMethode = $erkenningsnummerInvoerMethode;

        return $this;
    }

    /**
     * @return string
     */
    public function getErkenningsnummerInvoerWaarde()
    {
        return $this->erkenningsnummerInvoerWaarde;
    }

    /**
     * @param string $erkenningsnummerInvoerWaarde
     *
     */
    public function setErkenningsnummerInvoerWaarde($erkenningsnummerInvoerWaarde)
    {
        $this->erkenningsnummerInvoerWaarde = $erkenningsnummerInvoerWaarde;

        return $this;
    }

    /**
     * @return string
     */
    public function getAanwezig()
    {
        return $this->aanwezig;
    }

    /**
     * @param string $aanwezig
     *
     */
    public function setAanwezig($aanwezig)
    {
        $this->aanwezig = $aanwezig;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegistratieDatumtijd()
    {
        return $this->registratieDatumtijd;
    }

    /**
     * @param \DateTime $registratieDatumtijd
     *
     */
    public function setRegistratieDatumtijd($registratieDatumtijd)
    {
        $this->registratieDatumtijd = $registratieDatumtijd;

        return $this;
    }

    /**
     * @return float
     */
    public function getRegistratieGeolocatieLat()
    {
        return $this->registratieGeolocatieLat;
    }

    /**
     * @param float $registratieGeolocatieLat
     *
     */
    public function setRegistratieGeolocatieLat($registratieGeolocatieLat)
    {
        $this->registratieGeolocatieLat = $registratieGeolocatieLat;

        return $this;
    }

    /**
     * @return float
     */
    public function getRegistratieGeolocatieLong()
    {
        return $this->registratieGeolocatieLong;
    }

    /**
     * @param float $registratieGeolocatieLong
     *
     */
    public function setRegistratieGeolocatieLong($registratieGeolocatieLong)
    {
        $this->registratieGeolocatieLong = $registratieGeolocatieLong;

        return $this;
    }

    /**
     * @return Account
     */
    public function getRegistratieAccount()
    {
        return $this->registratieAccount;
    }

    /**
     * @param Account $registratieAccount
     *
     */
    public function setRegistratieAccount($registratieAccount)
    {
        $this->registratieAccount = $registratieAccount;

        return $this;
    }

    /**
     * @return int
     */
    public function getAantal3MeterKramen()
    {
        return $this->aantal3MeterKramen;
    }

    /**
     * @param int $aantal3MeterKramen
     *
     */
    public function setAantal3MeterKramen($aantal3MeterKramen)
    {
        $this->aantal3MeterKramen = $aantal3MeterKramen;

        return $this;
    }

    /**
     * @return int
     */
    public function getAantal4MeterKramen()
    {
        return $this->aantal4MeterKramen;
    }

    /**
     * @param int $aantal4MeterKramen
     *
     */
    public function setAantal4MeterKramen($aantal4MeterKramen)
    {
        $this->aantal4MeterKramen = $aantal4MeterKramen;

        return $this;
    }

    /**
     * @return int
     */
    public function getExtraMeters()
    {
        return $this->extraMeters;
    }

    /**
     * @param int $extraMeters
     *
     */
    public function setExtraMeters($extraMeters)
    {
        $this->extraMeters = $extraMeters;

        return $this;
    }

    /**
     * @return int
     */
    public function getAantalElektra()
    {
        return $this->aantalElektra;
    }

    /**
     * @param int $aantalElektra
     *
     */
    public function setAantalElektra($aantalElektra)
    {
        $this->aantalElektra = $aantalElektra;

        return $this;
    }

    /**
     * @return int
     */
    public function getAfvaleiland()
    {
        return $this->afvaleiland;
    }

    /**
     * @param int $afvaleiland
     *
     */
    public function setAfvaleiland($afvaleiland)
    {
        $this->afvaleiland = $afvaleiland;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEenmaligElektra()
    {
        return $this->eenmalig_elektra;
    }

    /**
     * @return bool
     */
    public function getEenmaligElektra()
    {
        return $this->eenmalig_elektra;
    }

    /**
     * @param bool $eenmalig_elektra
     *
     */
    public function setEenmaligElektra($eenmalig_elektra)
    {
        $this->eenmalig_elektra = $eenmalig_elektra;

        return $this;
    }

    /**
     * @return int
     */
    public function getAfvaleilandVast()
    {
        return $this->afvaleilandVast;
    }

    /**
     * @param int $afvaleilandVast
     *
     */
    public function setAfvaleilandVast($afvaleilandVast)
    {
        $this->afvaleilandVast = $afvaleilandVast;

        return $this;
    }

    /**
     * @return bool
     */
    public function isKrachtstroom()
    {
        return $this->krachtstroom;
    }

    /**
     * @return bool
     */
    public function getKrachtstroom()
    {
        return $this->krachtstroom;
    }

    /**
     * @param bool $krachtstroom
     *
     */
    public function setKrachtstroom($krachtstroom)
    {
        $this->krachtstroom = $krachtstroom;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReiniging()
    {
        return $this->reiniging;
    }

    /**
     * @return bool
     */
    public function getReiniging()
    {
        return $this->reiniging;
    }

    /**
     * @param bool $reiniging
     *
     */
    public function setReiniging($reiniging)
    {
        $this->reiniging = $reiniging;

        return $this;
    }

    /**
     * @return Sollicitatie
     */
    public function getSollicitatie()
    {
        return $this->sollicitatie;
    }

    /**
     * @param Sollicitatie $sollicitatie
     *
     */
    public function setSollicitatie($sollicitatie)
    {
        $this->sollicitatie = $sollicitatie;

        return $this;
    }

    /**
     * @return int
     */
    public function getAantal3meterKramenVast()
    {
        return $this->aantal3meterKramenVast;
    }

    /**
     * @param int $aantal3meterKramenVast
     *
     */
    public function setAantal3meterKramenVast($aantal3meterKramenVast)
    {
        $this->aantal3meterKramenVast = $aantal3meterKramenVast;

        return $this;
    }

    /**
     * @return int
     */
    public function getAantal4meterKramenVast()
    {
        return $this->aantal4meterKramenVast;
    }

    /**
     * @param int $aantal4meterKramenVast
     *
     */
    public function setAantal4meterKramenVast($aantal4meterKramenVast)
    {
        $this->aantal4meterKramenVast = $aantal4meterKramenVast;

        return $this;
    }

    /**
     * @return int
     */
    public function getAantalExtraMetersVast()
    {
        return $this->aantalExtraMetersVast;
    }

    /**
     * @param int $aantalExtraMetersVast
     *
     */
    public function setAantalExtraMetersVast($aantalExtraMetersVast)
    {
        $this->aantalExtraMetersVast = $aantalExtraMetersVast;

        return $this;
    }

    /**
     * @return int
     */
    public function getAantalElektraVast()
    {
        return $this->aantalElektraVast;
    }

    /**
     * @param int $aantalElektraVast
     *
     */
    public function setAantalElektraVast($aantalElektraVast)
    {
        $this->aantalElektraVast = $aantalElektraVast;

        return $this;
    }

    /**
     * @return bool
     */
    public function isKrachtstroomVast()
    {
        return $this->krachtstroomVast;
    }

    /**
     * @return bool
     */
    public function getKrachtstroomVast()
    {
        return $this->krachtstroomVast;
    }

    /**
     * @param bool $krachtstroomVast
     *
     */
    public function setKrachtstroomVast($krachtstroomVast)
    {
        $this->krachtstroomVast = $krachtstroomVast;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatusSolliciatie()
    {
        return $this->statusSolliciatie;
    }

    /**
     * @param string $statusSolliciatie
     *
     */
    public function setStatusSolliciatie($statusSolliciatie)
    {
        $this->statusSolliciatie = $statusSolliciatie;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotitie()
    {
        return $this->notitie;
    }

    /**
     * @param string $notitie
     *
     */
    public function setNotitie($notitie)
    {
        $this->notitie = $notitie;

        return $this;
    }
}
