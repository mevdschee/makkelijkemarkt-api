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

use App\Entity\Koopman;
use App\Mapper\KoopmanMapper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class KoopmanData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $fib1000 = function ($n) {
            list($n1, $n2) = [0, 1];
            for ($i = 0; $i < $n; $i++) {
                $n3 = $n2 + $n1;
                list($n1, $n2) = [$n2, $n3];
            }
            return $n2 % 1000;
        };

        $tussenvoegsels = ['', 'van der', 'van', 'van den', 'de', 'den'];

        for ($i = 0; $i < 10; $i++) {
            $nummer = "20200101.$i";
            $hash = md5($i);
            $year = 2020 - $i;

            $koopman = new Koopman();
            $koopman->setErkenningsnummer($nummer);
            $koopman->setVoorletters(chr(ord('A') + $i) . '.' . chr(ord('A') + $i + 4) . '.');
            $koopman->setAchternaam("Koopman$i");
            $koopman->setEmail("koopman$i@amsterdam.nl");
            $koopman->setTelefoon('06-1234' . sprintf('%04d', $i));
            $koopman->setPerfectViewNummer($fib1000($i + 20));
            $koopman->setFoto(abs(crc32($i)) . '-' . $hash . '-' . $nummer . '.jpg');
            $koopman->setStatus($i % count(KoopmanMapper::$statussen));
            $koopman->setFotoLastUpdate(new \DateTime("$year-01-01 00:00:00"));
            $koopman->setFotoHash($hash);
            $koopman->setPasUid(strtoupper(bin2hex(substr(md5($i, true), 0, 7))));
            $koopman->setTussenvoegsels($tussenvoegsels[$i % count($tussenvoegsels)]);
            $koopman->setHandhavingsVerzoek(null);
            $manager->persist($koopman);
        }

        $manager->flush();
    }
}
