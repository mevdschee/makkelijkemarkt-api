<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 *
 * @ORM\Entity(repositoryClass="MarktRepository")
 * @ORM\Table(indexes={
 *  @ORM\Index(name="marktAfkorting", columns={"afkorting"}),
 *  @ORM\Index(name="marktPerfectViewNumber", columns={"perfect_view_nummer"})
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
     * Init
     */
    public function __construct()
    {
        $this->sollicitaties = new ArrayCollection();
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
}
