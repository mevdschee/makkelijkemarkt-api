<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt;

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