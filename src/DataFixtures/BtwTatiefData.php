<?php
namespace App\DataFixtures;

use App\Entity\BtwTarief;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BtwTatiefData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($y = 2016; $y <= date('Y'); $y++) {
            $btwTarief = new BtwTarief();
            $btwTarief->setHoog(21);
            $btwTarief->setJaar($y);
            $manager->persist($btwTarief);
        }
        $manager->flush();
    }
}
