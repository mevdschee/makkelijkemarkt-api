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
 * @ORM\Table(indexes={@ORM\Index(name="dag_idx", columns={"dag"}), @ORM\Index(name="reason_idx", columns={"audit_reason"})});
 */
class Dagvergunning
{
    use MarktKraamTrait;

    const AUDIT_VERVANGER_ZONDER_TOESTEMMING = 'vervanger_zonder_toestemming';
    const AUDIT_HANDHAVINGS_VERZOEK = 'handhavings_verzoek';
    const AUDIT_LOTEN = 'loten';

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
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $audit;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $auditReason;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $loten;

    /**
     * @var Koopman
     *
     * @ORM\ManyToOne(targetEntity="Koopman", fetch="EAGER")
     * @ORM\JoinColumn(name="koopman_id", referencedColumnName="id", nullable=true)
     */
    private $koopman;

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
     * @var VergunningControle[]
     * @ORM\OneToMany(targetEntity="GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\VergunningControle", mappedBy="dagvergunning")
     */
    private $vergunningControles;


    /**
     * Init the entity
     */
    public function __construct()
    {
        $this->audit = false;
        $this->aanmaakDatumtijd = new \DateTime();
        $this->doorgehaald = false;
        $this->vergunningControles = new ArrayCollection();
        $this->auditReason = null;
        $this->loten = 0;
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
        if ($doorgehaaldDatumtijd !== null) {
            $this->verwijderdDatumtijd = new \DateTime();
        }
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

    public function getDoorgehaaldAccount()
    {
        return $this->doorgehaaldAccount;
    }

    public function setDoorgehaaldAccount(Account $account = null)
    {
        $this->doorgehaaldAccount = $account;
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
     * @return VergunningControle[]
     */
    public function getVergunningControles()
    {
        return $this->vergunningControles;
    }

    /**
     * @param VergunningControle[] $vergunningControles
     * @return Dagvergunning
     */
    public function setVergunningControles($vergunningControles)
    {
        $this->vergunningControles = $vergunningControles;

        return $this;
    }

    /**
     * @param VergunningControle $vergunningControle
     * @return Dagvergunning
     */
    public function addVergunningControle($vergunningControle)
    {
        $this->vergunningControles[] = $vergunningControle;

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
     * @return bool
     */
    public function isAudit()
    {
        return $this->audit;
    }

    /**
     * @return bool
     */
    public function getAudit()
    {
        return $this->audit;
    }

    /**
     * @param bool $audit
     * @return Dagvergunning
     */
    public function setAudit($audit)
    {
        $this->audit = $audit;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuditReason()
    {
        return $this->auditReason;
    }

    /**
     * @param string $auditReason
     * @return Dagvergunning
     */
    public function setAuditReason($auditReason)
    {
        $this->auditReason = $auditReason;

        return $this;
    }

    /**
     * @return integer
     */
    public function getLoten()
    {
        return $this->loten;
    }

    /**
     * @param integer $loten
     */
    public function setLoten($loten)
    {
        $this->loten = $loten;
    }


}