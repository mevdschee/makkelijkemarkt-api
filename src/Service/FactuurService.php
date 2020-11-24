<?php

namespace App\Service;

use App\Entity\Dagvergunning;
use App\Entity\Factuur;
use App\Entity\Product;
use App\Exception\FactuurServiceException;
use Doctrine\ORM\EntityManagerInterface;

class FactuurService
{

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ConcreetplanFactuurService
     */
    protected $concreetplanService;

    /**
     * @var LineairplanFactuurService
     */
    protected $lineairplanService;

    public function __construct(EntityManagerInterface $em,
        ConcreetplanFactuurService $concreetplanService,
        LineairplanFactuurService $lineairplanService) {
        $this->em = $em;
        $this->concreetplanService = $concreetplanService;
        $this->lineairplanService = $lineairplanService;
    }

    /**
     * @param Dagvergunning $dagvergunning
     * @return Factuur|null
     */
    public function createFactuur(Dagvergunning $dagvergunning)
    {
        $tariefplanRepo = $this->em->getRepository('AppApiBundle:Tariefplan');

        $tariefplan = $tariefplanRepo->findByMarktAndDag($dagvergunning->getMarkt(), $dagvergunning->getDag());

        if (null === $tariefplan) {
            return null;
        }

        $lineairplan = $tariefplan->getLineairplan();
        if (null === $lineairplan) {
            return $this->concreetplanService->createFactuur($dagvergunning, $tariefplan);
        } else {
            return $this->lineairplanService->createFactuur($dagvergunning, $tariefplan);
        }
    }

    public function removeFactuur(Dagvergunning $dagvergunning)
    {
        $factuur = $dagvergunning->getFactuur();
        if (null !== $factuur) {
            $producten = $factuur->getProducten();
            if (null !== $producten) {
                foreach ($producten as $product) {
                    $this->em->remove($product);
                }
            }
            $this->em->remove($factuur);
            $this->em->flush();
        }
    }

    public function saveFactuur(Factuur $factuur)
    {
        $this->em->persist($factuur);
        $producten = $factuur->getProducten();
        if (null !== $producten) {
            foreach ($producten as $product) {
                $this->em->persist($product);
            }
        }
        $this->em->flush();
    }

    /**
     * @param Factuur $factuur
     * @return number
     */
    public function getTotaal($factuur, $inclusiefBtw = true)
    {
        $totaal = 0;
        $producten = $factuur->getProducten();
        foreach ($producten as $product) {
            /**
             * @var Product $product
             */
            $totaal += number_format($product->getAantal() * $product->getBedrag() * ($inclusiefBtw ? ($product->getBtwHoog() / 100 + 1) : 1), 2);
        }

        return number_format($totaal, 2);
    }

    public function createDagvergunning($marktId,
        $dag,
        $erkenningsnummer,
        $aanwezig,
        $erkenningsnummerInvoerMethode,
        $registratieDatumtijd,
        $registratieGeolocatie,
        $aantal3MeterKramen,
        $aantal4MeterKramen,
        $extraMeters,
        $aantalElektra,
        $afvaleiland,
        $eenmaligElektra,
        $krachtstroom,
        $reiniging,
        $notitie,
        $user,
        $vervangerErkenningsnummer = null) {
        // create object
        $dagvergunning = new Dagvergunning();

        $repoMarkt = $this->em->getRepository('AppApiBundle:Markt');
        $repoKoopman = $this->em->getRepository('AppApiBundle:Koopman');

        // lookup markt
        $markt = $repoMarkt->getById($marktId);
        if ($markt === null) {
            return new FactuurServiceException(['No markt with id ' . $marktId . ' found']);
        }

        $dagvergunning->setMarkt($markt);

        // set aanwezig
        $dagvergunning->setAanwezig($aanwezig);

        // set erkenningsnummer info
        $dagvergunning->setErkenningsnummerInvoerWaarde(str_replace('.', '', $erkenningsnummer));
        $dagvergunning->setErkenningsnummerInvoerMethode($erkenningsnummerInvoerMethode);

        // lookup koopman
        $koopman = $repoKoopman->getByErkenningsnummer(str_replace('.', '', $erkenningsnummer));
        if ($koopman !== null) {
            $dagvergunning->setKoopman($koopman);
        }

        if (null !== $vervangerErkenningsnummer) {
            $vervanger = $repoKoopman->getByErkenningsnummer(str_replace('.', '', $vervangerErkenningsnummer));
            if ($vervanger !== null) {
                $dagvergunning->setVervanger($vervanger);
            }

        }

        // set geolocatie
        $point = self::parseGeolocation($registratieGeolocatie);
        $dagvergunning->setRegistratieGeolocatie($point[0], $point[1]);

        // set dag
        $dag = \DateTime::createFromFormat('Y-m-d', $dag);
        $dagvergunning->setDag($dag);

        // set registratie datum/tijd
        $registratieDatumtijd = \DateTime::createFromFormat('Y-m-d H:i:s', $registratieDatumtijd);
        $dagvergunning->setRegistratieDatumtijd($registratieDatumtijd);

        // set account
        $dagvergunning->setRegistratieAccount($user);

        // extras
        $dagvergunning->setAantal3MeterKramen(intval($aantal3MeterKramen));
        $dagvergunning->setAantal4MeterKramen(intval($aantal4MeterKramen));
        $dagvergunning->setExtraMeters(intval($extraMeters));
        $dagvergunning->setAantalElektra(intval($aantalElektra));
        $dagvergunning->setEenmaligElektra(boolval($eenmaligElektra));
        $dagvergunning->setAfvaleiland(intval($afvaleiland));
        $dagvergunning->setKrachtstroom(boolval($krachtstroom));
        $dagvergunning->setReiniging(boolval($reiniging));
        $dagvergunning->setNotitie($notitie);

        // sollicitatie koppeling
        $sollicitatieRepository = $this->em->getRepository('AppApiBundle:Sollicitatie');
        $sollicitatie = $sollicitatieRepository->getByMarktAndErkenningsNummer($markt, $erkenningsnummer, false);
        if ($sollicitatie !== null) {
            $dagvergunning->setAantal3meterKramenVast($sollicitatie->getAantal3MeterKramen());
            $dagvergunning->setAantal4meterKramenVast($sollicitatie->getAantal4MeterKramen());
            $dagvergunning->setAantalExtraMetersVast($sollicitatie->getAantalExtraMeters());
            $dagvergunning->setAantalElektraVast($sollicitatie->getAantalElektra());
            $dagvergunning->setKrachtstroomVast($sollicitatie->getKrachtstroom());
            $dagvergunning->setAfvaleilandVast($sollicitatie->getAantalAfvaleilanden());
            $dagvergunning->setSollicitatie($sollicitatie);
        }
        $dagvergunning->setStatusSolliciatie($sollicitatie !== null ? $sollicitatie->getStatus() : 'lot');

        return $dagvergunning;
    }

    /**
     * Helper to parse geolocation
     * @param mixed $geoInput
     * @return array tupple
     */
    public static function parseGeolocation($geoInput)
    {
        if ($geoInput === '' || $geoInput === null) {
            return [null, null];
        }

        if (is_array($geoInput) === false) {
            $geoInput = explode(',', $geoInput);
        }

        if (is_array($geoInput) === true) {
            if (count($geoInput) === 0 || count($geoInput) === 1) {
                return [null, null];
            }

            $geoInput = array_values($geoInput);
            $geoInput[0] = floatval($geoInput[0]);
            $geoInput[1] = floatval($geoInput[1]);
            return $geoInput;
        }
    }
}
