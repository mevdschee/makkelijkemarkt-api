<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Account;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Lineairplan;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Product;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Enum\Roles;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Service\FactuurService;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Concreetplan;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Tariefplan;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FactuurServiceTest extends KernelTestCase
{
    /**
     * @var FactuurService
     */
    protected $factuurService;

    /**
     * @var Markt
     */
    protected $markt;

    /**
     * @var Koopman
     */
    protected $koopman;

    /**
     * @var TariefPlan
     */
    protected $lineairTariefplan;

    /**
     * @var Sollicitatie
     */
    protected $sollicitatie;

    /**
     * @var Account
     */
    protected $account;

    /**
     * @var Dagvergunning[]
     */
    protected $dagvergunningen = array();

    /**
     * @var EntityManager
     */
    protected $em;

    public function setUp()
    {
        self::bootKernel();
        $this->factuurService = static::$kernel->getContainer()->get('appapi.factuurservice');
        $this->em             = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $this->em->getConnection()->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);


        $this->markt              = $this->getTestMarkt();
        $this->koopman            = $this->getTestKoopman();
        $this->lineairTariefplan  = $this->getTestLineairTariefplan();
        $this->sollicitatie       = $this->getTestSollicitatie();
        $this->account            = $this->getTestAccount();

        $this->lineairTariefplan->setMarkt($this->markt);
        $this->markt->addTariefplannen($this->lineairTariefplan);

        $this->sollicitatie->setMarkt($this->markt);
        $this->markt->addSollicitatie($this->sollicitatie);

        $this->sollicitatie->setKoopman($this->koopman);
        $this->koopman->addSollicitatie($this->sollicitatie);

        $this->em->persist($this->markt);
        $this->em->persist($this->koopman);
        $this->em->persist($this->lineairTariefplan);
        $lineairTariefplan = $this->lineairTariefplan->getLineairplan();
        $this->em->persist($lineairTariefplan);
        $this->em->persist($this->sollicitatie);
        $this->em->persist($this->account);
        $this->em->flush();
    }

    public function tearDown()
    {
        $this->markt->removeTariefplannen($this->lineairTariefplan);

        $this->markt->removeSollicitatie($this->sollicitatie);

        $this->koopman->removeSollicitatie($this->sollicitatie);

        $lineairPlan = $this->lineairTariefplan->getLineairplan();
        $this->lineairTariefplan->setLineairplan(null);
        $lineairPlan->setTariefplan(null);

        foreach ($this->dagvergunningen as $dagvergunning) {
            $this->koopman->removeDagvergunningen($dagvergunning);
        }

        $this->em->flush();

        foreach ($this->dagvergunningen as $dagvergunning) {
            $this->em->remove($dagvergunning);
        }

        $this->em->remove($this->markt);
        $this->em->remove($this->koopman);
        $this->em->remove($this->lineairTariefplan);
        $this->em->remove($lineairPlan);
        $this->em->remove($this->sollicitatie);
        $this->em->remove($this->account);
        $this->em->flush();

        $this->em->getConnection()->rollBack();
    }

    public function testLineairTariefplan()
    {
        $dagvergunning = new Dagvergunning();
        $vandaag = new \DateTime();
        $dagvergunning->setDag($vandaag);
        $dagvergunning->setErkenningsnummerInvoerMethode('scan-barcode');
        $dagvergunning->setErkenningsnummerInvoerWaarde('9000000');
        $dagvergunning->setAanwezig('zelf');
        $dagvergunning->setExtraMeters(3);
        $dagvergunning->setAantalExtraMetersVast(1);
        $dagvergunning->setAantal3MeterKramen(5);
        $dagvergunning->setAantal3meterKramenVast(2);
        $dagvergunning->setAantal4MeterKramen(3);
        $dagvergunning->setAantal4meterKramenVast(2);
        $dagvergunning->setAantalElektra(2);
        $dagvergunning->setAantalElektraVast(1);
        $dagvergunning->setRegistratieAccount($this->account);
        $dagvergunning->setMarkt($this->markt);
        $dagvergunning->setKoopman($this->koopman);
        $this->koopman->addDagvergunningen($dagvergunning);

        $this->dagvergunningen[] = $dagvergunning;
        $factuur = $this->factuurService->createFactuur($dagvergunning);

        echo "\nProducten:\n\n";
        $producten = $factuur->getProducten();

        foreach ($producten as $product) {
            /**
             * @var Product $product
             */
            echo number_format($product->getAantal() * $product->getBedrag() * ($product->getBtwHoog() / 100 + 1),2) . ' = ' . number_format($product->getAantal() * $product->getBedrag(),2) . ' = ' . $product->getAantal() . ' x ' . number_format($product->getBedrag(),2) . ' ' . $product->getNaam() . "\n";
        }

        echo "\nTotaal: " . $this->factuurService->getTotaal($factuur);
    }

    /**
     * @return Markt
     */
    protected function getTestMarkt() {
        $markt = new Markt();
        $markt->setNaam('Test markt');
        $markt->setAfkorting('TEST');
        $markt->setSoort('dag');
        $markt->setPerfectViewNummer(9000000);
        $markt->setStandaardKraamAfmeting(4);
        $markt->setExtraMetersMogelijk(true);
        $markt->setAanwezigeOpties(["3mKramen","4mKramen","extraMeters","elektra"]);
        return $markt;
    }

    /**
     * @return Koopman
     */
    protected function getTestKoopman() {
        $koopman = new Koopman();
        $koopman->setErkenningsnummer('9000000');
        $koopman->setVoorletters('T');
        $koopman->setAchternaam('Tester');
        $koopman->setEmail('t.tester@dev.null');
        $koopman->setTelefoon('0201234567');
        $koopman->setPerfectViewNummer(9000000);
        $koopman->setStatus(Koopman::STATUS_ACTIEF);
        return $koopman;
    }

    /**
     * @return Tariefplan
     */
    protected function getTestLineairTariefplan() {
        $tariefplan = new Tariefplan();
        $tariefplan->setNaam('Test lineair plan');
        $geldigVanaf = new \DateTime();
        $tariefplan->setGeldigVanaf($geldigVanaf);
        $geldigTot = clone $geldigVanaf;
        $geldigTot->modify('+1 year');
        $tariefplan->setGeldigTot($geldigTot);

        $lineairplan = new Lineairplan();
        $lineairplan->setTariefPerMeter(2.65);
        $lineairplan->setToeslagKrachtstroomPerAansluiting(5);
        $lineairplan->setReinigingPerMeter(0.20);
        $lineairplan->setToeslagBedrijfsafvalPerMeter(0.10);
        $lineairplan->setPromotieGeldenPerMeter(0.10);
        $lineairplan->setPromotieGeldenPerKraam(0);


        $tariefplan->setLineairplan($lineairplan);
        $lineairplan->setTariefplan($tariefplan);

        return $tariefplan;
    }

    /**
     * @return Sollicitatie
     */
    protected function getTestSollicitatie() {
        $sollicitatie = new Sollicitatie();
        $sollicitatie->setSollicitatieNummer(1);
        $sollicitatie->setStatus(Sollicitatie::STATUS_SOLL);
        $inschrijfdatum = new \DateTime('1995-03-02');
        $sollicitatie->setInschrijfDatum($inschrijfdatum);
        $sollicitatie->setDoorgehaald(false);
        $sollicitatie->setPerfectViewNummer(9000000);
        return $sollicitatie;
    }

    /**
     * @return Account
     */
    protected function getTestAccount() {
        $account = new Account();
        $account->setNaam('Test Account');
        $account->setEmail('test@dev.null');
        $account->setUsername('test@dev.null');
        $account->setPassword('abcd');
        $account->setRole(Roles::ROLE_USER);
        return $account;
    }
}
