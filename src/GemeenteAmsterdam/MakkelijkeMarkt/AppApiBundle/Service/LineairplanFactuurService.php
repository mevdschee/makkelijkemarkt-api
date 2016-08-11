<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Service;

use Doctrine\ORM\EntityManager;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Factuur;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Product;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Tariefplan;

class LineairplanFactuurService {

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Factuur
     */
    protected $factuur;

    /**
     * @var Tariefplan
     */
    protected $tariefplan;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * @param Dagvergunning $dagvergunning
     * @param Tariefplan $tariefplan
     * @return Factuur|null
     */
    public function createFactuur(Dagvergunning $dagvergunning, Tariefplan $tariefplan) {
        $this->tariefplan = $tariefplan;
        $this->factuur = new Factuur();
        $this->factuur->setDagvergunning($dagvergunning);
        $dagvergunning->setFactuur($this->factuur);

        $btwRepo = $this->em->getRepository('AppApiBundle:BtwTarief');
        $dag = $dagvergunning->getDag();

        $btwTarief = $btwRepo->findOneBy(array('jaar' => $dag->format('Y')));
        $btw = null !== $btwTarief ? $btwTarief->getHoog() : 0;

        list($totaalMeters, $totaalKramen) = $this->berekenMeters($dagvergunning, $btw);

        $this->berekenElektra($dagvergunning, $btw);
        $this->berekenEenmaligElektra($dagvergunning, $btw);

        $this->berekenAfvaleilanden($dagvergunning, $btw);

        $this->berekenPromotiegelden($totaalMeters, $totaalKramen, $dagvergunning);

        return $this->factuur;
    }

    protected function berekenMeters(Dagvergunning $dagvergunning, $btw) {
        $lineairplan = $this->tariefplan->getLineairplan();

        $meters[4] = $dagvergunning->getAantal4MeterKramen();
        $meters[3] = $dagvergunning->getAantal3MeterKramen();
        $meters[1] = $dagvergunning->getExtraMeters();

        $metersvast[4] = $dagvergunning->getAantal4meterKramenVast();
        $metersvast[3] = $dagvergunning->getAantal3meterKramenVast();
        $metersvast[1] = $dagvergunning->getAantalExtraMetersVast();

        $totaalKramen = 0;

        $tariefPerMeter = $lineairplan->getTariefPerMeter();
        $totaalMeters = $meters[4] * 4 + $meters[3] * 3 + $meters[1];
        $totaalMetersVast = $metersvast[4] * 4 + $metersvast[3] * 3 + $metersvast[1];

        if ($totaalMeters > 1) {
            $totaalKramen = 1;
        }

        $teBetalenMeters = $totaalMeters;
        if ($totaalMetersVast >= 1) {
            $teBetalenMeters = $teBetalenMeters - $totaalMetersVast;
            $product = new Product();
            $product->setNaam('afgenomen meters (vast)');
            $product->setBedrag(0);
            $product->setFactuur($this->factuur);
            $product->setAantal($totaalMetersVast);
            $product->setBtwHoog(0);
            $this->factuur->addProducten($product);

        }
        if ($teBetalenMeters >= 1) {
            $product = new Product();
            $product->setNaam('afgenomen meters');
            $product->setBedrag($tariefPerMeter);
            $product->setFactuur($this->factuur);
            $product->setAantal($teBetalenMeters);
            $product->setBtwHoog(0);
            $this->factuur->addProducten($product);

            $product = new Product();
            $product->setNaam('reiniging');
            $product->setBedrag($lineairplan->getReinigingPerMeter());
            $product->setFactuur($this->factuur);
            $product->setAantal($teBetalenMeters);
            $product->setBtwHoog($btw);
            $this->factuur->addProducten($product);

            $product = new Product();
            $product->setNaam('toeslag bedrijfsafval');
            $product->setBedrag($lineairplan->getToeslagBedrijfsafvalPerMeter());
            $product->setFactuur($this->factuur);
            $product->setAantal($teBetalenMeters);
            $product->setBtwHoog($btw);
            $this->factuur->addProducten($product);
        }

        return [$totaalMeters, $totaalKramen];
    }

    protected function berekenElektra(Dagvergunning $dagvergunning, $btw) {
        $lineairplan = $this->tariefplan->getLineairplan();

        $vast   = $dagvergunning->getAantalElektraVast();
        $afname = $dagvergunning->getAantalElektra();
        $kosten = $lineairplan->getToeslagKrachtstroomPerAansluiting();
        if (null !== $kosten && $kosten > 0 && $afname  >= 1) {
            if ($vast >= 1) {
                $afname = $afname - $vast;
                $product = new Product();
                $product->setNaam('elektra (vast)');
                $product->setBedrag(0);
                $product->setFactuur($this->factuur);
                $product->setAantal($vast);
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            }
            if ($afname >= 1) {
                $product = new Product();
                $product->setNaam('elektra');
                $product->setBedrag($kosten);
                $product->setFactuur($this->factuur);
                $product->setAantal($afname);
                $product->setBtwHoog($btw);
                $this->factuur->addProducten($product);
            }
        }
    }

    protected function berekenAfvaleilanden(Dagvergunning $dagvergunning, $btw) {
        $lineairplan = $this->tariefplan->getLineairplan();

        $vast   = $dagvergunning->getAfvaleilandVast();
        $afname = $dagvergunning->getAfvaleiland();
        $kosten = $lineairplan->getAfvaleiland();
        if (null !== $kosten && $kosten > 0 && $afname  >= 1) {
            if ($vast >= 1) {
                $afname = $afname - $vast;
                $product = new Product();
                $product->setNaam('afvaleiland (vast)');
                $product->setBedrag(0);
                $product->setFactuur($this->factuur);
                $product->setAantal($vast);
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            }
            if ($afname >= 1) {
                $product = new Product();
                $product->setNaam('afvaleiland');
                $product->setBedrag($kosten);
                $product->setFactuur($this->factuur);
                $product->setAantal($afname);
                $product->setBtwHoog($btw);
                $this->factuur->addProducten($product);
            }
        }
    }

    protected function berekenEenmaligElektra(Dagvergunning $dagvergunning, $btw) {
        $lineairplan = $this->tariefplan->getLineairplan();

        $eenmaligElektra = $dagvergunning->getEenmaligElektra();
        $kosten = $lineairplan->getEenmaligElektra();
        if (null !== $kosten && $kosten > 0 && true === $eenmaligElektra) {
            if (in_array($dagvergunning->getStatusSolliciatie(),array(Sollicitatie::STATUS_VKK, Sollicitatie::STATUS_VPL))) {
                $product = new Product();
                $product->setNaam('eenmalige elektra (vast)');
                $product->setBedrag(0);
                $product->setFactuur($this->factuur);
                $product->setAantal(1);
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            } else {
                $product = new Product();
                $product->setNaam('eenmalige elektra');
                $product->setBedrag($kosten);
                $product->setFactuur($this->factuur);
                $product->setAantal(1);
                $product->setBtwHoog($btw);
                $this->factuur->addProducten($product);
            }
        }
    }

    protected function berekenPromotiegelden($meters, $kramen, Dagvergunning $dagvergunning) {
        $lineairplan = $this->tariefplan->getLineairplan();

        $metersvast[4] = $dagvergunning->getAantal4meterKramenVast();
        $metersvast[3] = $dagvergunning->getAantal3meterKramenVast();
        $metersvast[1] = $dagvergunning->getAantalExtraMetersVast();

        $vasteMeters = $metersvast[4] * 4 + $metersvast[3] * 3 + $metersvast[1];


        $perKraam = $lineairplan->getPromotieGeldenPerKraam();
        if (null !== $perKraam && $perKraam > 0 && $kramen > 0) {
            if ($vasteMeters >= 1) {
                $product = new Product();
                $product->setNaam('promotiegelden per koopman (vast)');
                $product->setBedrag(0);
                $product->setFactuur($this->factuur);
                $product->setAantal($kramen);
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            } else {
                $product = new Product();
                $product->setNaam('promotiegelden per koopman');
                $product->setBedrag($perKraam);
                $product->setFactuur($this->factuur);
                $product->setAantal($kramen);
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            }
        }

        $perMeter = $lineairplan->getPromotieGeldenPerMeter();
        if (null !== $perMeter && $perMeter > 0 && $meters > 0) {
            if ($vasteMeters >= 1) {
                $meters = $meters - $vasteMeters;
                $product = new Product();
                $product->setNaam('promotiegelden per meter (vast)');
                $product->setBedrag(0);
                $product->setFactuur($this->factuur);
                $product->setAantal($vasteMeters);
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            }
            if ($meters >= 1) {
                $product = new Product();
                $product->setNaam('promotiegelden per meter');
                $product->setBedrag($perMeter);
                $product->setFactuur($this->factuur);
                $product->setAantal($meters);
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            }
        }
    }
}