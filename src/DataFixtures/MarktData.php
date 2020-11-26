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

        for ($i = 0; $i < 14; $i++) {
            $afkorting = "TM-$i";
            $nummer = $fib100($i + 10);

            $markt = new Markt();
            $markt->setNaam("TestMarkt$i");
            $markt->setAfkorting($afkorting);
            $markt->setPerfectViewNummer($nummer);
            $markt->setSoort($soorten[$i % count($soorten)]);
            $manager->persist($markt);

            $marktExtraData = new MarktExtraData($afkorting, $nummer);
            $marktExtraData->setMarktDagen(array_slice($dagen, 0, ($i % count($dagen)) + 1));
            $manager->persist($marktExtraData);
        }

        $manager->flush();
    }
}
