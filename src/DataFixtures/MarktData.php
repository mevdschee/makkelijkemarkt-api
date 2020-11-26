<?php
namespace App\DataFixtures;

use App\Entity\Markt;
use App\Entity\MarktExtraData;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MarktData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $fib100 = function ($n) {
            list($n1, $n2) = [0, 1];
            for ($i = 0; $i < $n; $i++) {
                $n3 = $n2 + $n1;
                list($n1, $n2) = [$n2, $n3];
            }
            return $n2 % 100;
        };

        $dagen = ['ma', 'di', 'wo', 'do', 'vr', 'za'];
        $soorten = ['dag', 'week', 'seizoen'];
        $aanwezigeOpties = ["3mKramen", "4mKramen", "extraMeters", "elektra", "afvaleiland"];

        for ($i = 0; $i < 14; $i++) {
            $afkorting = "TM-$i";
            $nummer = $fib100($i + 10);
            $kramen = pow(2, $i % 5 + 3);

            $markt = new Markt();
            $markt->setNaam("TestMarkt$i");
            $markt->setGeoArea(null);
            $markt->setAfkorting($afkorting);
            $markt->setSoort($soorten[$i % count($soorten)]);
            $markt->setPerfectViewNummer($nummer);
            if ($i % 2 == 1) {
                $markt->setMarktDagen($i % 2 == 0 ? [] : array_slice($dagen, 0, ($i % count($dagen)) + 1));
            }
            $markt->setStandaardKraamAfmeting($i % 4 < 3 ? 3 : 0);
            $markt->setExtraMetersMogelijk($i % 4 < 1);
            if ($i % 2 == 1) {
                $markt->setAanwezigeOpties(array_slice($aanwezigeOpties, 0, ($i % count($aanwezigeOpties)) + 1));
            }
            if ($i % 4 == 0) {
                $markt->setAantalKramen($kramen);
                $markt->setAantalMeter(floor($kramen * 3.55));
            }
            $markt->setAuditMax(10);
            $markt->setIndelingstype('traditioneel');
            $manager->persist($markt);

            $marktExtraData = new MarktExtraData($afkorting, $nummer);
            $marktExtraData->setMarktDagen(array_slice($dagen, 0, ($i % count($dagen)) + 1));
            $manager->persist($marktExtraData);
        }

        $manager->flush();
    }
}
