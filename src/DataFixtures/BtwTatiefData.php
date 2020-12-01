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

namespace App\DataFixtures;

use App\Entity\BtwTarief;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BtwTatiefData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($y = 2016; $y <= date('Y'); $y++) {
            $btwTarief = new BtwTarief();
            $btwTarief->setHoog(21);
            $btwTarief->setJaar($y);
            $manager->persist($btwTarief);
        }
        $manager->flush();
    }
}
