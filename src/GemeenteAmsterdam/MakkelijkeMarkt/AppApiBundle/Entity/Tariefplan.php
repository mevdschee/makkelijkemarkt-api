<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TariefplanRepository")
 * @ORM\Table
 */
class Tariefplan
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(type="string", nullable=false)
     */
    private $naam;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $geldigVanaf;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $geldigTot;

    /**
     * @var Lineairplan
     * @ORM\OneToOne(targetEntity="Lineairplan", cascade={"remove"})
     */
    protected $lineairplan;

    /**
     * @var Concreetplan
     * @ORM\OneToOne(targetEntity="Concreetplan", cascade={"remove"})
     */
    protected $concreetplan;

    /**
     * @var Markt
     * @ORM\ManyToOne(targetEntity="Markt", fetch="LAZY", inversedBy="tariefplannen")
     * @ORM\JoinColumn(name="markt_id", referencedColumnName="id", nullable=false)
     */
    private $markt;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set naam
     *
     * @param string $naam
     *
     * @return Tariefplan
     */
    public function setNaam($naam)
    {
        $this->naam = $naam;

        return $this;
    }

    /**
     * Get naam
     *
     * @return string
     */
    public function getNaam()
    {
        return $this->naam;
    }

    /**
     * Set geldigVanaf
     *
     * @param \DateTime $geldigVanaf
     *
     * @return Tariefplan
     */
    public function setGeldigVanaf($geldigVanaf)
    {
        $this->geldigVanaf = $geldigVanaf;

        return $this;
    }

    /**
     * Get geldigVanaf
     *
     * @return \DateTime
     */
    public function getGeldigVanaf()
    {
        return $this->geldigVanaf;
    }

    /**
     * Set geldigTot
     *
     * @param \DateTime $geldigTot
     *
     * @return Tariefplan
     */
    public function setGeldigTot($geldigTot)
    {
        $this->geldigTot = $geldigTot;

        return $this;
    }

    /**
     * Get geldigTot
     *
     * @return \DateTime
     */
    public function getGeldigTot()
    {
        return $this->geldigTot;
    }

    /**
     * Set lineairplan
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Lineairplan $lineairplan
     *
     * @return Tariefplan
     */
    public function setLineairplan(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Lineairplan $lineairplan = null)
    {
        $this->lineairplan = $lineairplan;

        return $this;
    }

    /**
     * Get lineairplan
     *
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Lineairplan
     */
    public function getLineairplan()
    {
        return $this->lineairplan;
    }

    /**
     * Set markt
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt $markt
     *
     * @return Tariefplan
     */
    public function setMarkt(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt $markt)
    {
        $this->markt = $markt;

        return $this;
    }

    /**
     * Get markt
     *
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function getMarkt()
    {
        return $this->markt;
    }

    /**
     * @return Concreetplan
     */
    public function getConcreetplan()
    {
        return $this->concreetplan;
    }

    /**
     * @param Concreetplan $concreetplan
     */
    public function setConcreetplan($concreetplan)
    {
        $this->concreetplan = $concreetplan;
    }


}
