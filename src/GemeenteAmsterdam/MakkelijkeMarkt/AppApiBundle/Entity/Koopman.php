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
 * @ORM\Entity(repositoryClass="KoopmanRepository")
 * @ORM\Table
 */
class Koopman
{
    /**
     * @var number
     */
    const STATUS_ONBEKEND = -1;

    /**
     * @var number
     */
    const STATUS_ACTIEF = 1;

    /**
     * @var number
     */
    const STATUS_VERWIJDERD = 0;

    /**
     * @var number
     */
    const STATUS_WACHTER = 2;

    /**
     * @var number
     */
    const STATUS_VERVANGER = 3;

    /**
     * @var number
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=255, nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $erkenningsnummer;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $voorletters;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tussenvoegsels;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $achternaam;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telefoon;

    /**
     * @var number
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $foto;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fotoLastUpdate;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $fotoHash;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $pasUid;

    /**
     * @var Sollicitatie[]
     * @ORM\OneToMany(targetEntity="Sollicitatie", mappedBy="koopman", fetch="LAZY", orphanRemoval=true)
     */
    private $sollicitaties;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=true)
     */
    private $perfectViewNummer;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true, options={"default": null})
     */
    private $handhavingsVerzoek;

    /**
     * @var Dagvergunning[]
     * @ORM\OneToMany(targetEntity="Dagvergunning", mappedBy="koopman", fetch="LAZY", orphanRemoval=true)
     */
    private $dagvergunningen;

    /**
     * @var Vervanger[]
     * @ORM\OneToMany(targetEntity="Vervanger", mappedBy="koopman", fetch="EXTRA_LAZY", orphanRemoval=true)
     */
    private $vervangersVan;

    /**
     * @var Vervanger[]
     * @ORM\OneToMany(targetEntity="Vervanger", mappedBy="vervanger", fetch="EXTRA_LAZY", orphanRemoval=true)
     */
    private $vervangerVoor;

    /**
     * Construct the entity
     */
    public function __construct()
    {
        $this->sollicitaties   = new ArrayCollection();
        $this->dagvergunningen = new ArrayCollection();
        $this->handhavingsVerzoek = null;
    }

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
    public function getErkenningsnummer()
    {
        return $this->erkenningsnummer;
    }

    /**
     * @return string
     */
    public function getVoorletters()
    {
        return $this->voorletters;
    }

    /**
     * @return string
     */
    public function getTussenvoegsels()
    {
        return $this->tussenvoegsels;
    }

    /**
     * @return string
     */
    public function getAchternaam()
    {
        return $this->achternaam;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getTelefoon()
    {
        return $this->telefoon;
    }

    /**
     * @return number
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * @return \DateTime|NULL
     */
    public function getFotoLastUpdate()
    {
        return $this->fotoLastUpdate;
    }

    /**
     * @return string
     */
    public function getFotoHash()
    {
        return $this->fotoHash;
    }

    /**
     * @return number
     */
    public function getPerfectViewNummer()
    {
        return $this->perfectViewNummer;
    }

    /**
     * @param string $erkenningsnummer
     */
    public function setErkenningsnummer($erkenningsnummer)
    {
        $this->erkenningsnummer = $erkenningsnummer;
    }

    /**
     * @param string $voorletters
     */
    public function setVoorletters($voorletters)
    {
        $this->voorletters = $voorletters;
    }

    /**
     * @param string $tussenvoegsels
     */
    public function setTussenvoegsels($tussenvoegsels)
    {
        $this->tussenvoegsels = $tussenvoegsels;
    }

    /**
     * @param string $achternaam
     */
    public function setAchternaam($achternaam)
    {
        $this->achternaam = $achternaam;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param string $telefoon
     */
    public function setTelefoon($telefoon)
    {
        $this->telefoon = $telefoon;
    }

    /**
     * @param number $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param string $foto
     */
    public function setFoto($foto)
    {
        $this->foto = $foto;
    }

    /**
     * @param \DateTime $fotoLastUpdate
     */
    public function setFotoLastUpdate(\DateTime $fotoLastUpdate = null)
    {
        $this->fotoLastUpdate = $fotoLastUpdate;
    }

    /**
     * @param string $fotoHash
     */
    public function setFotoHash($fotoHash = null)
    {
        $this->fotoHash = $fotoHash;
    }

    /**
     * @param number $perfectViewNummer
     */
    public function setPerfectViewNummer($perfectViewNummer)
    {
        $this->perfectViewNummer = $perfectViewNummer;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|Sollicitatie[]
     */
    public function getSollicitaties()
    {
        return $this->sollicitaties;
    }

    /**
     * @param Sollicitatie $sollicitatie
     */
    public function addSollicitatie(Sollicitatie $sollicitatie)
    {
        if ($this->hasSollicitatie($sollicitatie) === false)
            $this->sollicitaties->add($sollicitatie);
        if ($sollicitatie->getKoopman() !== $this)
            $sollicitatie->setKoopman($this);
    }

    /**
     * @param Sollicitatie $sollicitatie
     * @return boolean
     */
    public function hasSollicitatie(Sollicitatie $sollicitatie)
    {
        return $this->sollicitaties->contains($sollicitatie);
    }

    /**
     * @param Sollicitatie $sollicitatie
     */
    public function removeSollicitatie(Sollicitatie $sollicitatie)
    {
        if ($this->hasSollicitatie($sollicitatie) === true)
            $this->sollicitaties->removeElement($sollicitatie);
        if ($sollicitatie->getKoopman() === $this)
            $sollicitatie->setKoopman(null);
    }

    /**
     * Add dagvergunningen
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning $dagvergunningen
     *
     * @return Koopman
     */
    public function addDagvergunningen(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning $dagvergunningen)
    {
        $this->dagvergunningen[] = $dagvergunningen;

        return $this;
    }

    /**
     * Remove dagvergunningen
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning $dagvergunningen
     */
    public function removeDagvergunningen(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning $dagvergunningen)
    {
        $this->dagvergunningen->removeElement($dagvergunningen);
    }

    /**
     * Get dagvergunningen
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDagvergunningen()
    {
        return $this->dagvergunningen;
    }

    /**
     * Set pasUid
     *
     * @param string $pasUid
     *
     * @return Koopman
     */
    public function setPasUid($pasUid)
    {
        $this->pasUid = $pasUid;

        return $this;
    }

    /**
     * Get pasUid
     *
     * @return string
     */
    public function getPasUid()
    {
        return $this->pasUid;
    }

    /**
     * Add sollicitaty
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie $sollicitaty
     *
     * @return Koopman
     */
    public function addSollicitaty(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie $sollicitaty)
    {
        $this->sollicitaties[] = $sollicitaty;

        return $this;
    }

    /**
     * Remove sollicitaty
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie $sollicitaty
     */
    public function removeSollicitaty(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie $sollicitaty)
    {
        $this->sollicitaties->removeElement($sollicitaty);
    }

    /**
     * Add vervangersVan
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger $vervangersVan
     *
     * @return Koopman
     */
    public function addVervangersVan(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger $vervangersVan)
    {
        $this->vervangersVan[] = $vervangersVan;

        return $this;
    }

    /**
     * Remove vervangersVan
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger $vervangersVan
     */
    public function removeVervangersVan(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger $vervangersVan)
    {
        $this->vervangersVan->removeElement($vervangersVan);
    }

    /**
     * Get vervangersVan
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVervangersVan()
    {
        return $this->vervangersVan;
    }

    /**
     * Add vervangerVoor
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger $vervangerVoor
     *
     * @return Koopman
     */
    public function addVervangerVoor(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger $vervangerVoor)
    {
        $this->vervangerVoor[] = $vervangerVoor;

        return $this;
    }

    /**
     * Remove vervangerVoor
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger $vervangerVoor
     */
    public function removeVervangerVoor(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Vervanger $vervangerVoor)
    {
        $this->vervangerVoor->removeElement($vervangerVoor);
    }

    /**
     * Get vervangerVoor
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVervangerVoor()
    {
        return $this->vervangerVoor;
    }

    /**
     * @return \DateTime
     */
    public function getHandhavingsVerzoek()
    {
        return $this->handhavingsVerzoek;
    }

    /**
     * @param \DateTime $handhavingsVerzoek
     * @return Koopman
     */
    public function setHandhavingsVerzoek($handhavingsVerzoek)
    {
        $this->handhavingsVerzoek = $handhavingsVerzoek;

        return $this;
    }


    /**
     * @return float
     */
    public function calculateWeging() {
        $maandGeleden = new \DateTime();
        $maandGeleden->modify('-1 month');
        $maandGeleden->setTime(0,0,0);

        $dagvergunningen = 0;
        $afwezig = 0;

        foreach ($this->dagvergunningen as $dagvergunning) {
            if ($dagvergunning->getDag() < $maandGeleden) {
                continue;
            }

            $dagvergunningen++;

            foreach ($dagvergunning->getVergunningControles() as $controle) {
                if ('vervanger_zonder_toestemming' === $controle->getAanwezig()) {
                    $afwezig++;
                }
            }
        }
        if (0 === $dagvergunningen || 0 === $afwezig) {
            return 0;
        }
        return $afwezig / $dagvergunningen / 2;
    }
}