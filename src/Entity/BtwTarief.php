<?php
/*
 *  Copyright (c) 2020 X Gemeente
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
