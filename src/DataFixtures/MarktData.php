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

        //        COPY public.markt (id, naam, geo_area, afkorting, soort, perfect_view_nummer, markt_dagen, standaard_kraam_afmeting, extra_meters_mogelijk, aanwezige_opties, aantal_kramen, aantal_me
        //        ter, audit_max, kies_je_kraam_mededeling_actief, kies_je_kraam_mededeling_titel, kies_je_kraam_mededeling_tekst, kies_je_kraam_actief, kies_je_kraam_fase, markt_dagen_tekst, indeling
        //        s_tijdstip_tekst, telefoon_nummer_contact, makkelijke_markt_actief, kies_je_kraam_geblokkeerde_plaatsen, kies_je_kraam_geblokkeerde_data, kies_je_kraam_email_kramenzetter, indelingst
        //        ype) FROM stdin;
        //        id  naam      geo afkorting soort  pfn mkt_dgn, stdafm
        //        46    TestMarkt    \N    BWMMZ    seizoen    38    \N    3    f    ["3mKramen","4mKramen"]    \N    \N    10    f    \N    \N    f\N    \N    \N    \N    t    \N    \N    \N    traditioneel
        //        47    TestMarkt    \N    BWMZF    seizoen    39    \N    3    f    ["3mKramen","4mKramen"]    \N    \N    10    f    \N    \N    f    \N    \N    \N    \N    t    \N    \N    \N    traditioneel
        //        44    TestMarkt    \N    JMAMS    seizoen    36    \N    3    f    ["3mKramen","4mKramen"]    \N    \N    10    f    \N    \N    f    \N    \N    \N    \N    t    \N    \N    \N    traditioneel
        //        41    TestMarkt    \N    JM-MZ    seizoen    33    \N    3    f    ["3mKramen","4mKramen"]    \N    \N    10    f    \N    \N    f    \N    \N    \N    \N    t    \N    \N    \N    traditioneel
        //        42    TestMarkt    \N    JM-ZF    seizoen    34    \N    3    f    ["3mKramen","4mKramen"]    \N    \N    10    f    \N    \N    f    \N    \N    \N    \N    t    \N    \N    \N    traditioneel
        //        48    TestMarkt    \N    GEEN    seizoen    41    \N     0    f    \N    \N    \N    10    f    \N    \N    f    \N    \N    \N    \N    t    \N    \N    \N    traditioneel
        //        56    TestMarkt    \N    VENT    dag        \N    \N     0    f    []    \N    \N    10    f    \N    \N    f    \N    \N    \N    \N    t    \N    \N    \N    traditioneel
        //        52    TestMarkt    \N    KR      week    46    \N        3    t    ["3mKramen","4mKramen","extraMeters","elektra"]    82    328    10    f    \N    \N    f    \N    \N    \N    \N    t    \N    \N    \N    traditioneel
        //        55    TestMarkt    \N    STPL    week    \N    \N        0    f    []    \N    \N    10    f    \N    \N    f    \N    \N    \N    \N    t    \N    \N    \N    traditioneel
        //        31    TestMarkt    \N    NOM-Z    week    15    ma,za    3    t    ["3mKramen","4mKramen","extraMeters","elektra","afvaleiland"]    64    512    10    f    \N    \N    f    \N    \N    \N    \N    t    \N    \N    \N    traditioneel
        //

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
                $markt->setMarktDagen(array_slice($dagen, 0, ($i % count($dagen)) + 1));
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
            // meer velden
            $markt->setIndelingstype('traditioneel');
            $manager->persist($markt);

            $marktExtraData = new MarktExtraData($afkorting, $nummer);
            $marktExtraData->setMarktDagen(array_slice($dagen, 0, ($i % count($dagen)) + 1));
            $manager->persist($marktExtraData);
        }

        $manager->flush();
    }
}
