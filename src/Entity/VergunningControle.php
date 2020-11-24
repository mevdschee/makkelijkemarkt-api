<?php
/*
 *  Copyright (C) 2017 X Gemeente
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
 * @ORM\Entity
 * @ORM\Table
 */
class VergunningControle
{
    use MarktKraamTrait;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Dagvergunning
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Dagvergunning", fetch="EAGER")
     * @ORM\JoinColumn(name="dagvergunning_id", referencedColumnName="id", nullable=false)
     */
    private $dagvergunning;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ronde;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return VergunningControle
     */
    public function setId(int $id): VergunningControle
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Dagvergunning
     */
    public function getDagvergunning(): Dagvergunning
    {
        return $this->dagvergunning;
    }

    /**
     * @param Dagvergunning $dagvergunning
     * @return VergunningControle
     */
    public function setDagvergunning(Dagvergunning $dagvergunning): VergunningControle
    {
        $this->dagvergunning = $dagvergunning;

        return $this;
    }

    /**
     * @return int
     */
    public function getRonde()
    {
        return $this->ronde;
    }

    /**
     * @param int $ronde
     * @return VergunningControle
     */
    public function setRonde($ronde)
    {
        $this->ronde = $ronde;

        return $this;
    }
}
