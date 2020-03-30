<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table
 */
class Lineairplan
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
    private $tariefPerMeter;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $reinigingPerMeter;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $toeslagBedrijfsafvalPerMeter;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $toeslagKrachtstroomPerAansluiting;

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
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $elektra;

    /**
     * @var Tariefplan
     * @ORM\OneToOne(targetEntity="Tariefplan", inversedBy="lineairplan")
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
     * Set tariefPerMeter
     *
     * @param string $tariefPerMeter
     *
     * @return Lineairplan
     */
    public function setTariefPerMeter($tariefPerMeter)
    {
        $this->tariefPerMeter = $tariefPerMeter;

        return $this;
    }

    /**
     * Get tariefPerMeter
     *
     * @return string
     */
    public function getTariefPerMeter()
    {
        return $this->tariefPerMeter;
    }

    /**
     * Set reinigingPerMeter
     *
     * @param string $reinigingPerMeter
     *
     * @return Lineairplan
     */
    public function setReinigingPerMeter($reinigingPerMeter)
    {
        $this->reinigingPerMeter = $reinigingPerMeter;

        return $this;
    }

    /**
     * Get reinigingPerMeter
     *
     * @return string
     */
    public function getReinigingPerMeter()
    {
        return $this->reinigingPerMeter;
    }

    /**
     * Set toeslagBedrijfsafvalPerMeter
     *
     * @param string $toeslagBedrijfsafvalPerMeter
     *
     * @return Lineairplan
     */
    public function setToeslagBedrijfsafvalPerMeter($toeslagBedrijfsafvalPerMeter)
    {
        $this->toeslagBedrijfsafvalPerMeter = $toeslagBedrijfsafvalPerMeter;

        return $this;
    }

    /**
     * Get toeslagBedrijfsafvalPerMeter
     *
     * @return string
     */
    public function getToeslagBedrijfsafvalPerMeter()
    {
        return $this->toeslagBedrijfsafvalPerMeter;
    }

    /**
     * Set toeslagKrachtstroomPerAansluiting
     *
     * @param string $toeslagKrachtstroomPerAansluiting
     *
     * @return Lineairplan
     */
    public function setToeslagKrachtstroomPerAansluiting($toeslagKrachtstroomPerAansluiting)
    {
        $this->toeslagKrachtstroomPerAansluiting = $toeslagKrachtstroomPerAansluiting;

        return $this;
    }

    /**
     * Get toeslagKrachtstroomPerAansluiting
     *
     * @return string
     */
    public function getToeslagKrachtstroomPerAansluiting()
    {
        return $this->toeslagKrachtstroomPerAansluiting;
    }

    /**
     * Set tariefplan
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Tariefplan $tariefplan
     *
     * @return Lineairplan
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
     * @return float
     */
    public function getPromotieGeldenPerMeter()
    {
        return $this->promotieGeldenPerMeter;
    }

    /**
     * @param float $promotieGeldenPerMeter
     */
    public function setPromotieGeldenPerMeter($promotieGeldenPerMeter)
    {
        $this->promotieGeldenPerMeter = $promotieGeldenPerMeter;
    }

    /**
     * @return float
     */
    public function getPromotieGeldenPerKraam()
    {
        return $this->promotieGeldenPerKraam;
    }

    /**
     * @param float $promotieGeldenPerKraam
     */
    public function setPromotieGeldenPerKraam($promotieGeldenPerKraam)
    {
        $this->promotieGeldenPerKraam = $promotieGeldenPerKraam;
    }



    /**
     * Set afvaleiland
     *
     * @param string $afvaleiland
     *
     * @return Lineairplan
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
     * @return Lineairplan
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

    /**
     * Set elektra
     *
     * @param string $elektra
     *
     * @return Lineairplan
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
}