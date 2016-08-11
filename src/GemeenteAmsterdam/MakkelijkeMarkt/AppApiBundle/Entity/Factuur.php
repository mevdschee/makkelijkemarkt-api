<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="FactuurRepository")
 * @ORM\Table()
 */
class Factuur
{
    /**
     * @var number
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Dagvergunning
     * @ORM\OneToOne(targetEntity="Dagvergunning", inversedBy="factuur")
     * @ORM\JoinColumn(name="dagvergunning_id", referencedColumnName="id", nullable=true)
     */
    private $dagvergunning;

    /**
     * @var Product[]
     * @ORM\OneToMany(targetEntity="Product", mappedBy="factuur")
     */
    private $producten;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->producten = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set dagvergunning
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning $dagvergunning
     *
     * @return Factuur
     */
    public function setDagvergunning(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning $dagvergunning = null)
    {
        $this->dagvergunning = $dagvergunning;

        return $this;
    }

    /**
     * Get dagvergunning
     *
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning
     */
    public function getDagvergunning()
    {
        return $this->dagvergunning;
    }

    /**
     * Add producten
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Product $producten
     *
     * @return Factuur
     */
    public function addProducten(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Product $producten)
    {
        $this->producten[] = $producten;

        return $this;
    }

    /**
     * Remove producten
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Product $producten
     */
    public function removeProducten(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Product $producten)
    {
        $this->producten->removeElement($producten);
    }

    /**
     * Get producten
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducten()
    {
        return $this->producten;
    }
}