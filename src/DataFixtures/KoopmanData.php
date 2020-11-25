<?php
namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class KoopmanData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setNaam('product ' . $i);
            $product->setBedrag(mt_rand(10, 100));
            $product->setAantal(floor($i / 10) + 1);
            $product->setBtwHoog('21');
            $manager->persist($product);
        }

        $manager->flush();
    }
}
