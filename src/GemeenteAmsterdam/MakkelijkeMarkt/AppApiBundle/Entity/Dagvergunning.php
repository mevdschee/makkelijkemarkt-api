<?php
/*
 *  Copyright (C) 2017 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="DagvergunningRepository")
 * @ORM\Table
 */
class Dagvergunning
{
    /**
     * @var number
     *
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Markt
     *
     * @ORM\ManyToOne(targetEntity="Markt", fetch="EAGER")
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
     * @var Koopman
     *
     * @ORM\ManyToOne(targetEntity="Koopman", fetch="EAGER")
     * @ORM\JoinColumn(name="koopman_id", referencedColumnName="id", nullable=true)
     */
    private $koopman;

    /**
     * @var Koopman
     *
     * @ORM\ManyToOne(targetEntity="Koopman", fetch="EAGER")
     * @ORM\JoinColumn(name="vervanger_id", referencedColumnName="id", nullable=true)
     */
    private $vervanger;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $erkenningsnummerInvoerMethode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $erkenningsnummerInvoerWaarde;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $aanwezig;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $registratieDatumtijd;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $registratieGeolocatieLat;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $registratieGeolocatieLong;

    /**
     * @var Account
     * @ORM\ManyToOne(targetEntity="Account", fetch="LAZY")
     * @ORM\JoinColumn(name="registratie_account", referencedColumnName="id", nullable=true)
     */
    private $registratieAccount;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=false)
     */
    private $aantal3MeterKramen;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=false)
     */
    private $aantal4MeterKramen;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=false)
     */
    private $extraMeters;

    /**
     * @var number
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $aantalElektra;

    /**
     * @var number
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $afvaleiland;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $eenmalig_elektra;

    /**
     * @var number
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $afvaleilandVast;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $krachtstroom;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $reiniging;

    /**
     * @var Sollicitatie
     * @ORM\ManyToOne(targetEntity="Sollicitatie", fetch="LAZY")
     * @ORM\JoinColumn(name="sollicitatie_id", referencedColumnName="id", nullable=true)
     */
    private $sollicitatie;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=true)
     */
    private $aantal3meterKramenVast;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=true)
     */
    private $aantal4meterKramenVast;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=true)
     */
    private $aantalExtraMetersVast;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=true)
     */
    private $aantalElektraVast;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $krachtstroomVast;

    /**
     * @var string
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $statusSolliciatie;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $doorgehaald;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $doorgehaaldDatumtijd;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $doorgehaaldGeolocatieLat;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $doorgehaaldGeolocatieLong;

    /**
     * @var Account
     * @ORM\ManyToOne(targetEntity="Account", fetch="LAZY")
     * @ORM\JoinColumn(name="doorgehaald_account", referencedColumnName="id", nullable=true)
     */
    private $doorgehaaldAccount;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $notitie;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $aanmaakDatumtijd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $verwijderdDatumtijd;

    /**
     * @var Factuur
     * @ORM\OneToOne(targetEntity="Factuur", inversedBy="dagvergunning")
     */
    private $factuur;

    /**
     * Init the entity
     */
    public function __construct()
    {
        $this->aanmaakDatumtijd = new \DateTime();
        $this->doorgehaald = false;
    }

    /**
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function getMarkt()
    {
        return $this->markt;
    }

    /**
     * @return \DateTime
     */
    public function getDag()
    {
        return $this->dag;
    }

    /**
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman
     */
    public function getKoopman()
    {
        return $this->koopman;
    }

    /**
     * @return string
     */
    public function getErkenningsnummerInvoerMethode()
    {
        return $this->erkenningsnummerInvoerMethode;
    }

    /**
     * @return string
     */
    public function getErkenningsnummerInvoerWaarde()
    {
        return $this->erkenningsnummerInvoerWaarde;
    }

    /**
     * @return string
     */
    public function getAanwezig()
    {
        return $this->aanwezig;
    }

    /**
     * @return \DateTime
     */
    public function getRegistratieDatumtijd()
    {
        return $this->registratieDatumtijd;
    }

    /**
     * @return multitype:number
     */
    public function getRegistratieGeolocatie()
    {
        return [$this->registratieGeolocatieLat, $this->registratieGeolocatieLong];
    }

    /**
     * @return number
     */
    public function getAantal3MeterKramen()
    {
        return $this->aantal3MeterKramen;
    }

    /**
     * @return number
     */
    public function getAantal4MeterKramen()
    {
        return $this->aantal4MeterKramen;
    }

    /**
     * @return number
     */
    public function getExtraMeters()
    {
        return $this->extraMeters;
    }

    /**
     * @return boolean
     */
    public function isDoorgehaald()
    {
        return $this->doorgehaald;
    }

    /**
     * @return \DateTime
     */
    public function getDoorgehaaldDatumtijd()
    {
        return $this->doorgehaaldDatumtijd;
    }

    /**
     * @return multitype:number
     */
    public function getDoorgehaaldGeolocatie()
    {
        return [$this->doorgehaaldGeolocatieLat, $this->doorgehaaldGeolocatieLong];
    }

    /**
     * @param Markt $markt
     */
    public function setMarkt(Markt $markt)
    {
        $this->markt = $markt;
    }

    /**
     * @param \DateTime $dag
     */
    public function setDag(\DateTime $dag)
    {
        $this->dag = $dag;
    }

    /**
     * @param Koopman $koopman
     */
    public function setKoopman(Koopman $koopman)
    {
        $this->koopman = $koopman;
    }

    /**
     * @param string $erkenningsnummerInvoerWaarde
     */
    public function setErkenningsnummerInvoerWaarde($erkenningsnummerInvoerWaarde)
    {
        $this->erkenningsnummerInvoerWaarde = $erkenningsnummerInvoerWaarde;
    }

    /**
     * @param string $erkenningsnummerInvoerMethode Possible values: handmatig, opgezocht, scan-foto, scan-nfc, scan-qr, scan-barcode
     */
    public function setErkenningsnummerInvoerMethode($erkenningsnummerInvoerMethode)
    {
        $this->erkenningsnummerInvoerMethode = $erkenningsnummerInvoerMethode;
    }

    /**
     * @param string $aanwezig
     */
    public function setAanwezig($aanwezig)
    {
        $this->aanwezig = $aanwezig;
    }

    /**
     * @param \DateTime $registratie_datum
     */
    public function setRegistratieDatumtijd(\DateTime $registratieDatumtijd)
    {
        $this->registratieDatumtijd = $registratieDatumtijd;
    }

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
     * @param number $aantal3MeterKramen
     */
    public function setAantal3MeterKramen($aantal3MeterKramen)
    {
        $this->aantal3MeterKramen = $aantal3MeterKramen;
    }

    /**
     * @param number $aantal4MeterKramen
     */
    public function setAantal4MeterKramen($aantal4MeterKramen)
    {
        $this->aantal4MeterKramen = $aantal4MeterKramen;
    }

    /**
     * @param number $extraMeters
     */
    public function setExtraMeters($extraMeters)
    {
        $this->extraMeters = $extraMeters;
    }

    /**
     * @return number
     */
    public function getAantalElektra()
    {
        return $this->aantalElektra;
    }

    /**
     * @param number $aantalElektra
     */
    public function setAantalElektra($aantalElektra)
    {
        $this->aantalElektra = $aantalElektra;
    }

    /**
     * @return boolean
     */
    public function getKrachtstroom()
    {
        return $this->krachtstroom;
    }

    /**
     * @param boolean $krachtstroom
     */
    public function setKrachtstroom($krachtstroom)
    {
        $this->krachtstroom = $krachtstroom;
    }

    /**
     * @return boolean
     */
    public function getReiniging()
    {
        return $this->reiniging;
    }

    /**
     * @param boolean $reiniging
     */
    public function setReiniging($reiniging)
    {
        $this->reiniging = $reiniging;
    }

    /**
     * @param boolean $isDoorgehaald
     */
    public function setDoorgehaald($isDoorgehaald)
    {
        $this->doorgehaald = $isDoorgehaald;
    }

    /**
     * @param \DateTime $doorgehaaldDatumtijd
     */
    public function setDoorgehaaldDatumtijd(\DateTime $doorgehaaldDatumtijd = null)
    {
        $this->doorgehaaldDatumtijd = $doorgehaaldDatumtijd;
        if ($doorgehaaldDatumtijd !== null)
            $this->verwijderdDatumtijd = new \DateTime();
    }

    /**
     * @param float $lat
     * @param float $long
     */
    public function setDoorgehaaldGeolocatie($lat, $long)
    {
        $this->doorgehaaldGeolocatieLat = $lat;
        $this->doorgehaaldGeolocatieLong = $long;
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
     */
    public function setNotitie($notitie = null)
    {
        $this->notitie = $notitie;
    }

    /**
     * @return \DateTime
     */
    public function getAanmaakDatumtijd()
    {
        return $this->aanmaakDatumtijd;
    }

    /**
     * @return \DateTime
     */
    public function getVerwijderdDatumtijd()
    {
        return $this->verwijderdDatumtijd;
    }

    /**
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie
     */
    public function getSollicitatie()
    {
        return $this->sollicitatie;
    }

    /**
     * @param Sollicitatie $sollicitatie
     */
    public function setSollicitatie(Sollicitatie $sollicitatie)
    {
        $this->sollicitatie = $sollicitatie;
    }

    /**
     * @return number
     */
    public function getAantal3meterKramenVast()
    {
        return $this->aantal3meterKramenVast;
    }

    /**
     * @param number $aantal3meterKramenVast
     */
    public function setAantal3meterKramenVast($aantal3meterKramenVast)
    {
        $this->aantal3meterKramenVast = $aantal3meterKramenVast;
    }

    /**
     * @return number
     */
    public function getAantal4meterKramenVast()
    {
        return $this->aantal4meterKramenVast;
    }

    /**
     * @param number $aantal4meterKramenVast
     */
    public function setAantal4meterKramenVast($aantal4meterKramenVast)
    {
        $this->aantal4meterKramenVast = $aantal4meterKramenVast;
    }

    /**
     * @return number
     */
    public function getAantalExtraMetersVast()
    {
        return $this->aantalExtraMetersVast;
    }

    /**
     * @param number $aantalExtraMetersVast
     */
    public function setAantalExtraMetersVast($aantalExtraMetersVast)
    {
        $this->aantalExtraMetersVast = $aantalExtraMetersVast;
    }

    /**
     * @return number
     */
    public function getAantalElektraVast()
    {
        return $this->aantalElektraVast;
    }

    /**
     * @param number $aantalElektraVast
     */
    public function setAantalElektraVast($aantalElektraVast)
    {
        $this->aantalElektraVast = $aantalElektraVast;
    }

    /**
     * @return boolean
     */
    public function getKrachtstroomVast()
    {
        return $this->krachtstroomVast;
    }

    /**
     * @param boolean $krachtstroomVast
     */
    public function setKrachtstroomVast($krachtstroomVast)
    {
        $this->krachtstroomVast = $krachtstroomVast;
    }

    /**
     * @return string
     */
    public function getStatusSollicitatie()
    {
        return $this->statusSolliciatie;
    }

    /**
     * @param string $statusSollicitatie
     */
    public function setStatusSollicitatie($statusSollicitatie)
    {
        $this->statusSolliciatie = $statusSollicitatie;
    }

    public function getRegistratieAccount()
    {
        return $this->registratieAccount;
    }

    public function setRegistratieAccount(Account $account = null)
    {
        $this->registratieAccount = $account;
    }

    public function getDoorgehaaldAccount()
    {
        return $this->doorgehaaldAccount;
    }

    public function setDoorgehaaldAccount(Account $account = null)
    {
        $this->doorgehaaldAccount = $account;
    }

    /**
     * Set registratieGeolocatieLat
     *
     * @param float $registratieGeolocatieLat
     *
     * @return Dagvergunning
     */
    public function setRegistratieGeolocatieLat($registratieGeolocatieLat)
    {
        $this->registratieGeolocatieLat = $registratieGeolocatieLat;

        return $this;
    }

    /**
     * Get registratieGeolocatieLat
     *
     * @return float
     */
    public function getRegistratieGeolocatieLat()
    {
        return $this->registratieGeolocatieLat;
    }

    /**
     * Set registratieGeolocatieLong
     *
     * @param float $registratieGeolocatieLong
     *
     * @return Dagvergunning
     */
    public function setRegistratieGeolocatieLong($registratieGeolocatieLong)
    {
        $this->registratieGeolocatieLong = $registratieGeolocatieLong;

        return $this;
    }

    /**
     * Get registratieGeolocatieLong
     *
     * @return float
     */
    public function getRegistratieGeolocatieLong()
    {
        return $this->registratieGeolocatieLong;
    }

    /**
     * Set statusSolliciatie
     *
     * @param string $statusSolliciatie
     *
     * @return Dagvergunning
     */
    public function setStatusSolliciatie($statusSolliciatie)
    {
        $this->statusSolliciatie = $statusSolliciatie;

        return $this;
    }

    /**
     * Get statusSolliciatie
     *
     * @return string
     */
    public function getStatusSolliciatie()
    {
        return $this->statusSolliciatie;
    }

    /**
     * Get doorgehaald
     *
     * @return boolean
     */
    public function getDoorgehaald()
    {
        return $this->doorgehaald;
    }

    /**
     * Set doorgehaaldGeolocatieLat
     *
     * @param float $doorgehaaldGeolocatieLat
     *
     * @return Dagvergunning
     */
    public function setDoorgehaaldGeolocatieLat($doorgehaaldGeolocatieLat)
    {
        $this->doorgehaaldGeolocatieLat = $doorgehaaldGeolocatieLat;

        return $this;
    }

    /**
     * Get doorgehaaldGeolocatieLat
     *
     * @return float
     */
    public function getDoorgehaaldGeolocatieLat()
    {
        return $this->doorgehaaldGeolocatieLat;
    }

    /**
     * Set doorgehaaldGeolocatieLong
     *
     * @param float $doorgehaaldGeolocatieLong
     *
     * @return Dagvergunning
     */
    public function setDoorgehaaldGeolocatieLong($doorgehaaldGeolocatieLong)
    {
        $this->doorgehaaldGeolocatieLong = $doorgehaaldGeolocatieLong;

        return $this;
    }

    /**
     * Get doorgehaaldGeolocatieLong
     *
     * @return float
     */
    public function getDoorgehaaldGeolocatieLong()
    {
        return $this->doorgehaaldGeolocatieLong;
    }

    /**
     * Set aanmaakDatumtijd
     *
     * @param \DateTime $aanmaakDatumtijd
     *
     * @return Dagvergunning
     */
    public function setAanmaakDatumtijd($aanmaakDatumtijd)
    {
        $this->aanmaakDatumtijd = $aanmaakDatumtijd;

        return $this;
    }

    /**
     * Set verwijderdDatumtijd
     *
     * @param \DateTime $verwijderdDatumtijd
     *
     * @return Dagvergunning
     */
    public function setVerwijderdDatumtijd($verwijderdDatumtijd)
    {
        $this->verwijderdDatumtijd = $verwijderdDatumtijd;

        return $this;
    }

    /**
     * Set factuur
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Factuur $factuur
     *
     * @return Dagvergunning
     */
    public function setFactuur(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Factuur $factuur = null)
    {
        $this->factuur = $factuur;

        return $this;
    }

    /**
     * Get factuur
     *
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Factuur
     */
    public function getFactuur()
    {
        return $this->factuur;
    }

    /**
     * Set afvaleiland
     *
     * @param integer $afvaleiland
     *
     * @return Dagvergunning
     */
    public function setAfvaleiland($afvaleiland)
    {
        $this->afvaleiland = $afvaleiland;

        return $this;
    }

    /**
     * Get afvaleiland
     *
     * @return integer
     */
    public function getAfvaleiland()
    {
        return $this->afvaleiland;
    }

    /**
     * Set afvaleilandVast
     *
     * @param integer $afvaleilandVast
     *
     * @return Dagvergunning
     */
    public function setAfvaleilandVast($afvaleilandVast)
    {
        $this->afvaleilandVast = $afvaleilandVast;

        return $this;
    }

    /**
     * Get afvaleilandVast
     *
     * @return integer
     */
    public function getAfvaleilandVast()
    {
        return $this->afvaleilandVast;
    }

    /**
     * Set eenmaligElektra
     *
     * @param boolean $eenmaligElektra
     *
     * @return Dagvergunning
     */
    public function setEenmaligElektra($eenmaligElektra)
    {
        $this->eenmalig_elektra = $eenmaligElektra;

        return $this;
    }

    /**
     * Get eenmaligElektra
     *
     * @return boolean
     */
    public function getEenmaligElektra()
    {
        return $this->eenmalig_elektra;
    }

    /**
     * Set vervanger
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman $vervanger
     *
     * @return Dagvergunning
     */
    public function setVervanger(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman $vervanger = null)
    {
        $this->vervanger = $vervanger;

        return $this;
    }

    /**
     * Get vervanger
     *
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman
     */
    public function getVervanger()
    {
        return $this->vervanger;
    }
}