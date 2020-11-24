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

namespace App\DataFixtures\ORM;

use App\Entity\Koopman;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class KoopmanData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $koopman = new Koopman();
        $koopman->setAchternaam('Geraets');
        $koopman->setVoorletters('M');
        $koopman->setEmail('m.geraets@amsterdam.nl');
        $koopman->setTelefoon('06-55512345');
        $koopman->setErkenningsnummer('19000806.23');
        $manager->persist($koopman);

        $koopman = new Koopman();
        $koopman->setAchternaam('De Keizer');
        $koopman->setVoorletters('M');
        $koopman->setEmail('m.de.keizer@amsterdam.nl');
        $koopman->setTelefoon('06-55571161');
        $koopman->setErkenningsnummer('19000806.25');
        $manager->persist($koopman);

        $koopman = new Koopman();
        $koopman->setAchternaam('De Mey');
        $koopman->setVoorletters('Y');
        $koopman->setEmail('y.de.mey@amsterdam.nl');
        $koopman->setTelefoon('06-55546432');
        $koopman->setErkenningsnummer('19000806.26');
        $manager->persist($koopman);

        $koopman = new Koopman();
        $koopman->setAchternaam('Groenen');
        $koopman->setVoorletters('J');
        $koopman->setEmail('j.groenen@amsterdam.nl');
        $koopman->setTelefoon('06-55583434');
        $koopman->setErkenningsnummer('19000809.01');
        $manager->persist($koopman);

        $manager->flush();
    }
}
