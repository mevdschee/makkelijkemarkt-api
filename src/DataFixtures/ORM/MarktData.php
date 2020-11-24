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

use App\Entity\Markt;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class MarktData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $markt = new Markt();
        $markt->setNaam('Nieuwmarkt');
        $markt->setAfkorting('NW');
        $markt->setPerfectViewNummer(13);
        $markt->setSoort(Markt::SOORT_DAG);
        $manager->persist($markt);

        $markt = new Markt();
        $markt->setNaam('Dapperstraat');
        $markt->setAfkorting('DAPP');
        $markt->setPerfectViewNummer(6);
        $markt->setSoort(Markt::SOORT_DAG);
        $manager->persist($markt);

        $markt = new Markt();
        $markt->setNaam('Maandag Noordermarkt');
        $markt->setAfkorting('NOM-M');
        $markt->setPerfectViewNummer(40);
        $markt->setSoort(Markt::SOORT_WEEK);
        $manager->persist($markt);

        $markt = new Markt();
        $markt->setNaam('Waterlooplein');
        $markt->setAfkorting('WAT');
        $markt->setPerfectViewNummer(26);
        $markt->setSoort(Markt::SOORT_DAG);
        $manager->persist($markt);

        $markt = new Markt();
        $markt->setNaam('Albert Cuypstraat');
        $markt->setAfkorting('AC');
        $markt->setPerfectViewNummer(1);
        $markt->setSoort(Markt::SOORT_DAG);
        $manager->persist($markt);

        $markt = new Markt();
        $markt->setNaam('Plein 40/45');
        $markt->setAfkorting('4045');
        $markt->setPerfectViewNummer(19);
        $markt->setSoort(Markt::SOORT_DAG);
        $manager->persist($markt);

        $markt = new Markt();
        $markt->setNaam('van der Pekstraat');
        $markt->setAfkorting('PEK');
        $markt->setPerfectViewNummer(12);
        $markt->setSoort(Markt::SOORT_DAG);
        $manager->persist($markt);

        $manager->flush();
    }
}
