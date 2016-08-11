<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table
 */
class BtwTarief
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
    private $hoog;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    private $jaar;

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
     * Set hoog
     *
     * @param string $hoog
     *
     * @return BtwTarief
     */
    public function setHoog($hoog)
    {
        $this->hoog = $hoog;

        return $this;
    }

    /**
     * Get hoog
     *
     * @return string
     */
    public function getHoog()
    {
        return $this->hoog;
    }

    /**
     * Set jaar
     *
     * @param integer $jaar
     *
     * @return BtwTarief
     */
    public function setJaar($jaar)
    {
        $this->jaar = $jaar;

        return $this;
    }

    /**
     * Get jaar
     *
     * @return integer
     */
    public function getJaar()
    {
        return $this->jaar;
    }
}