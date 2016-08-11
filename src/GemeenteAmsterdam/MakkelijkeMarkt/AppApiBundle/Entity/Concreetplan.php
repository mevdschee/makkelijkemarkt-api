<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table
 */
class Concreetplan
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $een_meter;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $drie_meter;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $vier_meter;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $elektra;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $promotieGeldenPerMeter;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $promotieGeldenPerKraam;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $afvaleiland;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $eenmaligElektra;

    /**
     * @var Tariefplan
     * @ORM\OneToOne(targetEntity="Tariefplan", inversedBy="concreetplan")
     * @ORM\JoinColumn(name="tariefplan_id", referencedColumnName="id")
     */
    private $tariefplan;

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
     * Set eenMeter
     *
     * @param string $eenMeter
     *
     * @return Concreetplan
     */
    public function setEenMeter($eenMeter)
    {
        $this->een_meter = $eenMeter;

        return $this;
    }

    /**
     * Get eenMeter
     *
     * @return string
     */
    public function getEenMeter()
    {
        return $this->een_meter;
    }

    /**
     * Set drieMeter
     *
     * @param string $drieMeter
     *
     * @return Concreetplan
     */
    public function setDrieMeter($drieMeter)
    {
        $this->drie_meter = $drieMeter;

        return $this;
    }

    /**
     * Get drieMeter
     *
     * @return string
     */
    public function getDrieMeter()
    {
        return $this->drie_meter;
    }

    /**
     * Set vierMeter
     *
     * @param string $vierMeter
     *
     * @return Concreetplan
     */
    public function setVierMeter($vierMeter)
    {
        $this->vier_meter = $vierMeter;

        return $this;
    }

    /**
     * Get vierMeter
     *
     * @return string
     */
    public function getVierMeter()
    {
        return $this->vier_meter;
    }

    /**
     * Set elektra
     *
     * @param string $elektra
     *
     * @return Concreetplan
     */
    public function setElektra($elektra)
    {
        $this->elektra = $elektra;

        return $this;
    }

    /**
     * Get elektra
     *
     * @return string
     */
    public function getElektra()
    {
        return $this->elektra;
    }

    /**
     * Set promotieGeldenPerMeter
     *
     * @param string $promotieGeldenPerMeter
     *
     * @return Concreetplan
     */
    public function setPromotieGeldenPerMeter($promotieGeldenPerMeter)
    {
        $this->promotieGeldenPerMeter = $promotieGeldenPerMeter;

        return $this;
    }

    /**
     * Get promotieGeldenPerMeter
     *
     * @return string
     */
    public function getPromotieGeldenPerMeter()
    {
        return $this->promotieGeldenPerMeter;
    }

    /**
     * Set promotieGeldenPerKraam
     *
     * @param string $promotieGeldenPerKraam
     *
     * @return Concreetplan
     */
    public function setPromotieGeldenPerKraam($promotieGeldenPerKraam)
    {
        $this->promotieGeldenPerKraam = $promotieGeldenPerKraam;

        return $this;
    }

    /**
     * Get promotieGeldenPerKraam
     *
     * @return string
     */
    public function getPromotieGeldenPerKraam()
    {
        return $this->promotieGeldenPerKraam;
    }

    /**
     * Set tariefplan
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Tariefplan $tariefplan
     *
     * @return Concreetplan
     */
    public function setTariefplan(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Tariefplan $tariefplan = null)
    {
        $this->tariefplan = $tariefplan;

        return $this;
    }

    /**
     * Get tariefplan
     *
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Tariefplan
     */
    public function getTariefplan()
    {
        return $this->tariefplan;
    }

    /**
     * Set afvaleiland
     *
     * @param string $afvaleiland
     *
     * @return Concreetplan
     */
    public function setAfvaleiland($afvaleiland)
    {
        $this->afvaleiland = $afvaleiland;

        return $this;
    }

    /**
     * Get afvaleiland
     *
     * @return string
     */
    public function getAfvaleiland()
    {
        return $this->afvaleiland;
    }

    /**
     * Set eenmaligElektra
     *
     * @param string $eenmaligElektra
     *
     * @return Concreetplan
     */
    public function setEenmaligElektra($eenmaligElektra)
    {
        $this->eenmaligElektra = $eenmaligElektra;

        return $this;
    }

    /**
     * Get eenmaligElektra
     *
     * @return string
     */
    public function getEenmaligElektra()
    {
        return $this->eenmaligElektra;
    }
}