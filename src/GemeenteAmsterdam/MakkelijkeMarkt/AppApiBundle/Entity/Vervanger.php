<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="")
 * @ORM\Table
 */
class Vervanger
{
    /**
     * @var number
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=255, nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Koopman
     * @ORM\ManyToOne(targetEntity="Koopman", fetch="LAZY", inversedBy="vervangersVan")
     * @ORM\JoinColumn(name="koopman_id", referencedColumnName="id", nullable=false)
     */
    private $koopman;

    /**
     * @var Koopman
     * @ORM\ManyToOne(targetEntity="Koopman", fetch="LAZY", inversedBy="vervangerVoor")
     * @ORM\JoinColumn(name="vervanger_id", referencedColumnName="id", nullable=false)
     */
    private $vervanger;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $pasUid;

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
     * Set pasUid
     *
     * @param string $pasUid
     *
     * @return Vervanger
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
     * Set koopman
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman $koopman
     *
     * @return Vervanger
     */
    public function setKoopman(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman $koopman)
    {
        $this->koopman = $koopman;

        return $this;
    }

    /**
     * Get koopman
     *
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman
     */
    public function getKoopman()
    {
        return $this->koopman;
    }

    /**
     * Set vervanger
     *
     * @param \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman $vervanger
     *
     * @return Vervanger
     */
    public function setVervanger(\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman $vervanger)
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