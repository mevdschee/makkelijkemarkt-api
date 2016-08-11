<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 *
 * @ORM\Entity(repositoryClass="MarktExtraDataRepository")
 * @ORM\Table()
 */
class MarktExtraData
{
    /**
     * @var number
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     */
    private $perfectViewNummer;

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
     * @var multitype:string
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $aanwezigeOpties;

    /**
     * @param number $perfectViewNummer
     */
    public function __construct($perfectViewNummer)
    {
        $this->perfectViewNummer = $perfectViewNummer;
    }

    /**
     * @return number
     */
    public function getPerfectViewNummer()
    {
        return $this->perfectViewNummer;
    }

    /**
     * @return string
     */
    public function getGeoArea()
    {
        return $this->geoArea;
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
     * @param array $aanwezigeOpties
     */
    public function setAanwezigeOpties(array $aanwezigeOpties = [])
    {
        $this->aanwezigeOpties = $aanwezigeOpties;
    }
}