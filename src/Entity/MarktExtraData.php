<?php
/*
 *  Copyright (C) 2020 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="MarktExtraDataRepository")
 * @ORM\Table()
 */
class MarktExtraData
{
    /**
     * @var number
     * @ORM\Column(type="integer", nullable=true)
     */
    private $perfectViewNummer;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", nullable=false)
     */
    private $afkorting;

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
     * @var string
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
     * @return string
     */
    public function getAanwezigeOpties()
    {
        if ($this->aanwezigeOpties === null) {
            return [];
        }

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
     * @return string
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
        foreach ($marktDagen as $marktDag) {
            if (in_array($marktDag, ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo']) === false) {
                throw new \InvalidArgumentException('Invalid marktDag supplied "' . $marktDag . '" only ma, di, wo, do, vr, za, zo are allowed');
            }

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
