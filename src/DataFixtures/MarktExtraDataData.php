<?php
namespace App\DataFixtures;

use App\Entity\MarktExtraData;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MarktExtraDataData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $marktExtraData = new MarktExtraData('NW', 13); // nieuwmarkt, algemene waren
        $marktExtraData->setMarktDagen(['ma', 'di', 'wo', 'do', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData('WAT', 26); // waterlooplein, algemene waren
        $marktExtraData->setMarktDagen(['ma', 'di', 'wo', 'do', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData('NOM-M', 40); // noordermarkt, algemene waren
        $marktExtraData->setMarktDagen(['za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData('4045', 19); // plein40-45, algemene waren
        $marktExtraData->setMarktDagen(['di', 'wo', 'do', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData('PEK', 12); // pekmarkt, algemene waren
        $marktExtraData->setMarktDagen(['wo', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData('DAPP', 6); // dapperstraat, algemene waren
        $marktExtraData->setMarktDagen(['ma', 'di', 'wo', 'do', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $marktExtraData = new MarktExtraData('AC', 1); // albert cuypstraat, algemene waren
        $marktExtraData->setMarktDagen(['ma', 'di', 'wo', 'do', 'vr', 'za']);
        $manager->persist($marktExtraData);

        $manager->flush();
    }
}
