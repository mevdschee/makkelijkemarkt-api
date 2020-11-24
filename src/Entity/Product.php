<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class Product
{
    /**
     * @var number
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $naam;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $bedrag;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $btwHoog;

    /**
     * @var float
     * @ORM\Column(type="integer")
     */
    private $aantal;

    /**
     * @var Factuur
     * @ORM\ManyToOne(targetEntity="Factuur")
     * @ORM\JoinColumn(name="factuur_id", referencedColumnName="id", nullable=true)
     */
    private $factuur;

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
     * @return Product
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
     * Set bedrag
     *
     * @param string $bedrag
     *
     * @return Product
     */
    public function setBedrag($bedrag)
    {
        $this->bedrag = $bedrag;

        return $this;
    }

    /**
     * Get bedrag
     *
     * @return string
     */
    public function getBedrag()
    {
        return $this->bedrag;
    }

    /**
     * Set factuur
     *
     * @param \App\Entity\Factuur $factuur
     *
     * @return Product
     */
    public function setFactuur(\App\Entity\Factuur $factuur = null)
    {
        $this->factuur = $factuur;

        return $this;
    }

    /**
     * Get factuur
     *
     * @return \App\Entity\Factuur
     */
    public function getFactuur()
    {
        return $this->factuur;
    }

    /**
     * Set aantal
     *
     * @param integer $aantal
     *
     * @return Product
     */
    public function setAantal($aantal)
    {
        $this->aantal = $aantal;

        return $this;
    }

    /**
     * Get aantal
     *
     * @return integer
     */
    public function getAantal()
    {
        return $this->aantal;
    }

    /**
     * Set btwHoog
     *
     * @param string $btwHoog
     *
     * @return Product
     */
    public function setBtwHoog($btwHoog)
    {
        $this->btwHoog = $btwHoog;

        return $this;
    }

    /**
     * Get btwHoog
     *
     * @return string
     */
    public function getBtwHoog()
    {
        return $this->btwHoog;
    }
}
