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
 * @ORM\Entity(repositoryClass="MarktRepository")
 * @ORM\Table(indexes={
 *  @ORM\Index(name="marktPerfectViewNumber", columns={"perfect_view_nummer"})
 * }, uniqueConstraints={
 *  @ORM\UniqueConstraint(name="marktAfkorting", columns={"afkorting"})
 * })
 */
class Markt
{
    /**
     * @var string
     */
    const SOORT_DAG = 'dag';

    /**
     * @var string
     */
    const SOORT_WEEK = 'week';

    /**
     * @var string
     */
    const SOORT_SEIZOEN = 'seizoen';
    
    /**
     * @var string
     */
    const KIESJEKRAAM_FASE_VOORBEREIDING = 'voorbereiding';
    
    /**
     * @var string
     */
    const KIESJEKRAAM_FASE_ACTIVATIE = 'activatie';
    
    /**
     * @var string
     */
    const KIESJEKRAAM_FASE_WENPERIODE = 'wenperiode';
    
    /**
     * @var string
     */
    const KIESJEKRAAM_FASE_LIVE = 'live';

    /**
     * @var number
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private $afkorting;

    /**
     * @var string
     * @ORM\Column(type="string", length=125, nullable=false)
     */
    private $naam;

    /**
     * @var string
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private $soort;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $geoArea;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $marktDagen;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=true)
     */
    private $standaardKraamAfmeting;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $extraMetersMogelijk;

    /**
     * @var multitype:string
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $aanwezigeOpties;

    /**
     * @var Sollicitatie[]
     * @ORM\OneToMany(targetEntity="Sollicitatie", mappedBy="markt", fetch="EXTRA_LAZY", orphanRemoval=true)
     * @ORM\OrderBy({"sollicitatieNummer"="ASC"})
     */
    private $sollicitaties;

    /**
     * @var Tariefplan[]
     * @ORM\OneToMany(targetEntity="Tariefplan", mappedBy="markt", fetch="EXTRA_LAZY", orphanRemoval=true)
     * @ORM\OrderBy({"geldigVanaf"="DESC"})
     */
    private $tariefplannen;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=true)
     */
    private $perfectViewNummer;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=true)
     */
    private $aantalKramen;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=true)
     */
    private $aantalMeter;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false, options={"default": 10})
     */
    private $auditMax;
    
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $kiesJeKraamMededelingActief;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $kiesJeKraamMededelingTitel;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $kiesJeKraamMededelingTekst;
    
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $kiesJeKraamActief;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $kiesJeKraamFase;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $kiesJeKraamGeblokkeerdePlaatsen;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $kiesJeKraamGeblokkeerdeData;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $kiesJeKraamEmailKramenzetter;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $marktDagenTekst;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $indelingsTijdstipTekst;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $telefoonNummerContact;
    
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $makkelijkeMarktActief;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $indelingstype;

    /**
     * Init
     */
    public function __construct()
    {
        $this->auditMax = 10;
        $this->sollicitaties = new ArrayCollection();
        $this->kiesJeKraamActief = false;
        $this->makkelijkeMarktActief = true;
        $this->kiesJeKraamMededelingActief = false;
        $this->indelingstype = 'a/b-lijst';
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
    public function getAfkorting()
    {
        return $this->afkorting;
    }

    /**
     * @param string $afkorting
     */
    public function setAfkorting($afkorting)
    {
        $this->afkorting = $afkorting;
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
    public function getGeoArea()
    {
        return $this->geoArea;
    }

    /**
     * @param string $geoArea
     */
    public function setGeoArea($geoArea)
    {
        $this->geoArea = $geoArea;
    }

    /**
     * @return multitype:string
     */
    public function getMarktDagen()
    {
        return explode(',', $this->marktDagen);
    }

    /**
     * @param array $marktDagen
     */
    public function setMarktDagen(array $marktDagen = [])
    {
        foreach ($marktDagen as $marktDag)
        {
            if (in_array($marktDag, ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo']) === false)
                throw new \InvalidArgumentException('Invalid marktDag supplied "' . $marktDag . '" only ma, di, wo, do, vr, za, zo are allowed');
        }
        $this->marktDagen = implode(',', $marktDagen);
    }

    /**
     * @param string $dag ma|di|wo|do|vr|za|zo
     */
    public function hasMarktDag($dag)
    {
        return in_array($dag, $this->getMarktDagen());
    }

    /**
     * @return string
     */
    public function getSoort()
    {
        return $this->soort;
    }

    /**
     * @param string $soort
     */
    public function setSoort($soort)
    {
        $this->soort = $soort;
    }

    /**
     * @return number
     */
    public function getStandaardKraamAfmeting()
    {
        return $this->standaardKraamAfmeting;
    }

    /**
     * @param number $standaardKraamAfmeting
     */
    public function setStandaardKraamAfmeting($standaardKraamAfmeting)
    {
        $this->standaardKraamAfmeting = $standaardKraamAfmeting;
    }

    /**
     * @return boolean
     */
    public function getExtraMetersMogelijk()
    {
        return $this->extraMetersMogelijk;
    }

    /**
     * @param boolean $extraMetersMogelijk
     */
    public function setExtraMetersMogelijk($extraMetersMogelijk)
    {
        $this->extraMetersMogelijk = $extraMetersMogelijk;
    }

    /**
     * @return multitype:string
     */
    public function getAanwezigeOpties()
    {
        if ($this->aanwezigeOpties === null)
            return [];
        return $this->aanwezigeOpties;
    }

    /**
     * @param array $aanwezigeOpties
     */
    public function setAanwezigeOpties(array $aanwezigeOpties = [])
    {
        $this->aanwezigeOpties = $aanwezigeOpties;
    }

    /**
     * @return Sollicitatie[]
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
        if ($sollicitatie->getMarkt() !== $this)
            $sollicitatie->setMarkt($this);
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
        if ($sollicitatie->getMarkt() === $this)
            $sollicitatie->setMarkt(null);
    }

    /**
     * @return number
     */
    public function getPerfectViewNummer()
    {
        return $this->perfectViewNummer;
    }

    /**
     * @param number $perfectViewNummer
     */
    public function setPerfectViewNummer($perfectViewNummer)
    {
        $this->perfectViewNummer = $perfectViewNummer;
    }

    /**
     * Add tariefplannen
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Tariefplan $tariefplannen
     *
     * @return Markt
     */
    public function addTariefplannen(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Tariefplan $tariefplannen)
    {
        $this->tariefplannen[] = $tariefplannen;

        return $this;
    }

    /**
     * Remove tariefplannen
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Tariefplan $tariefplannen
     */
    public function removeTariefplannen(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Tariefplan $tariefplannen)
    {
        $this->tariefplannen->removeElement($tariefplannen);
    }

    /**
     * Get tariefplannen
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTariefplannen()
    {
        return $this->tariefplannen;
    }

    /**
     * @return number
     */
    public function getAantalKramen()
    {
        return $this->aantalKramen;
    }

    /**
     * @param number $aantalKramen
     */
    public function setAantalKramen($aantalKramen)
    {
        $this->aantalKramen = $aantalKramen;
    }

    /**
     * @return number
     */
    public function getAantalMeter()
    {
        return $this->aantalMeter;
    }

    /**
     * @param number $aantalMeter
     */
    public function setAantalMeter($aantalMeter)
    {
        $this->aantalMeter = $aantalMeter;
    }

    /**
     * @return int
     */
    public function getAuditMax()
    {
        return $this->auditMax;
    }

    /**
     * @param int $auditMax
     * @return Markt
     */
    public function setAuditMax($auditMax)
    {
        $this->auditMax = $auditMax;

        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getKiesJeKraamMededelingActief()
    {
        return $this->kiesJeKraamMededelingActief;
    }
    
    /**
     * @param string $kiesJeKraamMededelingActief
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function setKiesJeKraamMededelingActief($kiesJeKraamMededelingActief)
    {
        $this->kiesJeKraamMededelingActief = $kiesJeKraamMededelingActief;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getKiesJeKraamMededelingTitel()
    {
        return $this->kiesJeKraamMededelingTitel;
    }
    
    /**
     * @param string $kiesJeKraamMededelingTitel
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function setKiesJeKraamMededelingTitel($kiesJeKraamMededelingTitel)
    {
        $this->kiesJeKraamMededelingTitel = $kiesJeKraamMededelingTitel;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getKiesJeKraamMededelingTekst()
    {
        return $this->kiesJeKraamMededelingTekst;
    }
    
    /**
     * @param string $kiesJeKraamMededelingTekst
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function setKiesJeKraamMededelingTekst($kiesJeKraamMededelingTekst)
    {
        $this->kiesJeKraamMededelingTekst = $kiesJeKraamMededelingTekst;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getKiesJeKraamGeblokkeerdePlaatsen()
    {
        return $this->kiesJeKraamGeblokkeerdePlaatsen;
    }
    
    /**
     * @param string $kiesJeKraamGeblokkeerdePlaatsen
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function setKiesJeKraamGeblokkeerdePlaatsen($kiesJeKraamGeblokkeerdePlaatsen)
    {
        $this->kiesJeKraamGeblokkeerdePlaatsen = $kiesJeKraamGeblokkeerdePlaatsen;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getKiesJeKraamGeblokkeerdeData()
    {
        return $this->kiesJeKraamGeblokkeerdeData;
    }
    
    /**
     * @param string $kiesJeKraamGeblokkeerdeData
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function setKiesJeKraamGeblokkeerdeData($kiesJeKraamGeblokkeerdeData)
    {
        $this->kiesJeKraamGeblokkeerdeData = $kiesJeKraamGeblokkeerdeData;
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getKiesJeKraamActief()
    {
        return $this->kiesJeKraamActief;
    }
    
    /**
     * @param string $kiesJeKraamActief
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function setKiesJeKraamActief($kiesJeKraamActief)
    {
        $this->kiesJeKraamActief = $kiesJeKraamActief;
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getMakkelijkeMarktActief()
    {
        return $this->makkelijkeMarktActief;
    }
    
    /**
     * @param string $makkelijkeMarktActief
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function setMakkelijkeMarktActief($makkelijkeMarktActief)
    {
        $this->makkelijkeMarktActief = $makkelijkeMarktActief;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getKiesJeKraamFase()
    {
        return $this->kiesJeKraamFase;
    }
    
    /**
     * @param string $kiesJeKraamFase
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function setKiesJeKraamFase($kiesJeKraamFase)
    {
        $this->kiesJeKraamFase = $kiesJeKraamFase;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getKiesJeKraamEmailKramenzetter()
    {
        return $this->kiesJeKraamEmailKramenzetter;
    }
    
    /**
     * @param string $kiesJeKraamEmailKramenzetter
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function setKiesJeKraamEmailKramenzetter($kiesJeKraamEmailKramenzetter = null)
    {
        $this->kiesJeKraamEmailKramenzetter = $kiesJeKraamEmailKramenzetter;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getMarktDagenTekst()
    {
        return $this->marktDagenTekst;
    }
    
    /**
     * @param string $marktDagenTekst
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function setMarktDagenTekst($marktDagenTekst)
    {
        $this->marktDagenTekst = $marktDagenTekst;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getIndelingsTijdstipTekst()
    {
        return $this->indelingsTijdstipTekst;
    }
    
    /**
     * @param string $indelingsTijdstipTekst
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function setIndelingsTijdstipTekst($indelingsTijdstipTekst)
    {
        $this->indelingsTijdstipTekst = $indelingsTijdstipTekst;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getTelefoonNummerContact()
    {
        return $this->telefoonNummerContact;
    }
    
    /**
     * @param string $telefoonNummerContact
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function setTelefoonNummerContact($telefoonNummerContact)
    {
        $this->telefoonNummerContact = $telefoonNummerContact;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getIndelingstype()
    {
        return $this->indelingstype;
    }
    
    /**
     * @param string $indelingstype
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function setIndelingstype($indelingstype)
    {
        $this->indelingstype = $indelingstype;
        
        return $this;
    }
}
