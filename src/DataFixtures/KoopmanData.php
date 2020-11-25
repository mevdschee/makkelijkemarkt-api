<?php
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

        for ($i = 0; $i < 5; $i++) {
            $nummer = "19000806.2$i";
            $hash = md5($i);

            $koopman = new Koopman();
            $koopman->setErkenningsnummer($nummer);
            $koopman->setVoorletters('K.M.');
            $koopman->setAchternaam("Koopman$i");
            $koopman->setEmail("koopman$i@amsterdam.nl");
            $koopman->setTelefoon('06-1234' . sprintf('%04d', $i));
            $koopman->setPerfectViewNummer($fib1000($i + 20));
            $koopman->setFoto(abs(crc32($i)) . '-' . $hash . '-' . $nummer . '.jpg');
            $koopman->setStatus($i % count(KoopmanMapper::$statussen));
            $koopman->setFotoLastUpdate(new \DateTime("$i day ago"));
            $koopman->setFotoHash($hash);
            $koopman->setPasUid(strtoupper(bin2hex(substr(md5($i, true), 0, 7))));
            $koopman->setTussenvoegsels('van der');
            $koopman->setHandhavingsVerzoek(null);
            $manager->persist($koopman);
        }

        $manager->flush();
    }
}
