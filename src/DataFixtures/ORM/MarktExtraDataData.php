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

use App\Entity\MarktExtraData;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class MarktExtraDataData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $marktExtraData = new MarktExtraData(13); // nieuwmarkt, algemene waren
        $marktExtraData->setMarktDagen(['ma', 'di', 'wo', 'do', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(26); // waterlooplein, algemene waren
        $marktExtraData->setMarktDagen(['ma', 'di', 'wo', 'do', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(2); // amstelveld, bloemen en planten
        $marktExtraData->setMarktDagen(['ma']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(42); // haarlemmerplein, boerenmarkt
        $marktExtraData->setMarktDagen(['wo']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(11); // lindengracht, algemene waren
        $marktExtraData->setMarktDagen(['za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(14); // nieuwmarkt, bioversemarkt
        $marktExtraData->setMarktDagen(['za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(16); // noordermarkt, boerenmarkt
        $marktExtraData->setMarktDagen(['za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(40); // noordermarkt, algemene waren
        $marktExtraData->setMarktDagen(['za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(15); // noordermarkt, algemene waren
        $marktExtraData->setMarktDagen(['ma', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(21); // spui, boekenmarkt
        $marktExtraData->setMarktDagen(['vr']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(31); // spui, kunstmarkt
        $marktExtraData->setMarktDagen(['zo']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(27); // westerstraat, kunstmarkt
        $marktExtraData->setMarktDagen(['ma']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(28); // tussenmeer, algemene waren
        $marktExtraData->setMarktDagen(['di']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(19); // plein40-45, algemene waren
        $marktExtraData->setMarktDagen(['di', 'wo', 'do', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(10); // lambertus zijlplein, algemene waren
        $marktExtraData->setMarktDagen(['ma']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(5); // buikslotermeerplein, algemene waren
        $marktExtraData->setMarktDagen(['ma', 'di', 'wo', 'do', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(12); // pekmarkt, algemene waren
        $marktExtraData->setMarktDagen(['wo', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(6); // dapperstraat, algemene waren
        $marktExtraData->setMarktDagen(['ma', 'di', 'wo', 'do', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(43); // van eesterenlaan, boerenmarkt
        $marktExtraData->setMarktDagen(['wo']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(9); // bos en lommerplein, algemene waren
        $marktExtraData->setMarktDagen(['di', 'wo', 'do', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(24); // ten katestraat, algemene waren
        $marktExtraData->setMarktDagen(['ma', 'di', 'wo', 'do', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(1); // albert cuypstraat, algemene waren
        $marktExtraData->setMarktDagen(['ma', 'di', 'wo', 'do', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(22); // stadionplein, algemene waren
        $marktExtraData->setMarktDagen(['za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(20); // reigersbos, algemene waren
        $marktExtraData->setMarktDagen(['wo']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(7); // fazantenhof, algemene waren
        $marktExtraData->setMarktDagen(['do']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData(8); // ganzenhoef, algemene waren
        $marktExtraData->setMarktDagen(['za']);
        $manager->persist($marktExtraData);

        $manager->flush();
    }
}
