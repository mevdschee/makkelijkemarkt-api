<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Service;

use Doctrine\ORM\EntityManager;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Factuur;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Product;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Tariefplan;

class ConcreetplanFactuurService {

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


        list($totaalMeters, $totaalKramen) = $this->berekenMeters($dagvergunning);

        $this->berekenElektra($dagvergunning);

        $this->berekenEenmaligElektra($dagvergunning);

        $this->berekenPromotiegelden($totaalMeters, $totaalKramen, $dagvergunning);

        $btwRepo = $this->em->getRepository('AppApiBundle:BtwTarief');
        $dag = $dagvergunning->getDag();

        $btwTarief = $btwRepo->findOneBy(array('jaar' => $dag->format('Y')));
        $btw = null !== $btwTarief ? $btwTarief->getHoog() : 0;

        $this->berekenAfvaleilanden($dagvergunning, $btw);

        return $this->factuur;
    }

    protected function berekenMeters(Dagvergunning $dagvergunning) {
        $concreetplan = $this->tariefplan->getConcreetplan();

        $meters[4] = $dagvergunning->getAantal4MeterKramen();
        $meters[3] = $dagvergunning->getAantal3MeterKramen();
        $meters[1] = $dagvergunning->getExtraMeters();

        $metersvast[4] = $dagvergunning->getAantal4meterKramenVast();
        $metersvast[3] = $dagvergunning->getAantal3meterKramenVast();
        $metersvast[1] = $dagvergunning->getAantalExtraMetersVast();


        $totaalMeters = 0;
        $totaalKramen = 0;

        $vierMeter = $concreetplan->getVierMeter();
        if (null !== $vierMeter && $vierMeter > 0 && $meters[4] >= 1) {
            $nietFacturabeleMeters = 0;
            $facturabeleMeters     = 0;
            while($meters[4] >= 1) {
                if ($metersvast[4] >= 1) {
                    $metersvast[4]--;
                    $facturabeleMeters++;
                } else {
                    $nietFacturabeleMeters++;
                }
                $meters[4]--;
                $totaalMeters += 4;
                $totaalKramen = 1;
            }
            if ($facturabeleMeters >= 1) {
                $product = new Product();
                $product->setNaam('4 meter plaats (vast)');
                $product->setBedrag(0);
                $product->setFactuur($this->factuur);
                $product->setAantal($facturabeleMeters);
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            }
            if ($nietFacturabeleMeters >= 1) {
                $product = new Product();
                $product->setNaam('4 meter plaats');
                $product->setBedrag($vierMeter);
                $product->setFactuur($this->factuur);
                $product->setAantal($nietFacturabeleMeters);
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            }
        }

        $drieMeter = $concreetplan->getDrieMeter();
        if (null !== $drieMeter && $drieMeter > 0 && $meters[3] >= 1) {
            $nietFacturabeleMeters = 0;
            $facturabeleMeters     = 0;
            while($meters[3] >= 1) {
                if ($metersvast[3] >= 1) {
                    $metersvast[3]--;
                    $facturabeleMeters++;
                } else {
                    $nietFacturabeleMeters++;
                }
                $meters[3]--;
                $totaalMeters += 3;
                $totaalKramen = 1;
            }
            if ($facturabeleMeters >= 1) {
                $product = new Product();
                $product->setNaam('3 meter plaats (vast)');
                $product->setBedrag(0);
                $product->setFactuur($this->factuur);
                $product->setAantal($facturabeleMeters);
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            }
            if ($nietFacturabeleMeters >= 1) {
                $product = new Product();
                $product->setNaam('3 meter plaats');
                $product->setBedrag($drieMeter);
                $product->setFactuur($this->factuur);
                $product->setAantal($nietFacturabeleMeters);
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            }
        }

        $eenMeter = $concreetplan->getEenMeter();
        if (null !== $eenMeter && $eenMeter > 0 && $meters[1] >= 1) {
            $nietFacturabeleMeters = 0;
            $facturabeleMeters     = 0;
            while($meters[1] >= 1) {
                if ($metersvast[1] >= 1) {
                    $metersvast[1]--;
                    $facturabeleMeters++;
                } else {
                    $nietFacturabeleMeters++;
                }
                $meters[1]--;
                $totaalMeters += 1;
            }
            if ($facturabeleMeters >= 1) {
                $product = new Product();
                $product->setNaam('extra meter (vast)');
                $product->setBedrag(0);
                $product->setFactuur($this->factuur);
                $product->setAantal($facturabeleMeters);
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            }
            if ($nietFacturabeleMeters >= 1) {
                $product = new Product();
                $product->setNaam('extra meter');
                $product->setBedrag($eenMeter);
                $product->setFactuur($this->factuur);
                $product->setAantal($nietFacturabeleMeters);
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            }
        }

        return [$totaalMeters, $totaalKramen];
    }

    protected function berekenElektra(Dagvergunning $dagvergunning) {
        $concreetplan = $this->tariefplan->getConcreetplan();

        $vast   = $dagvergunning->getAantalElektraVast();
        $afname = $dagvergunning->getAantalElektra();
        $kosten = $concreetplan->getElektra();
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
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            }
        }
    }

    protected function berekenEenmaligElektra(Dagvergunning $dagvergunning) {
        $concreetplan = $this->tariefplan->getConcreetplan();

        $eenmaligElektra = $dagvergunning->getEenmaligElektra();
        $kosten = $concreetplan->getEenmaligElektra();
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
                $product->setBtwHoog(0);
                $this->factuur->addProducten($product);
            }
        }
    }

    protected function berekenPromotiegelden($meters, $kramen, Dagvergunning $dagvergunning) {
        $concreetplan = $this->tariefplan->getConcreetplan();

        $metersvast[4] = $dagvergunning->getAantal4meterKramenVast();
        $metersvast[3] = $dagvergunning->getAantal3meterKramenVast();
        $metersvast[1] = $dagvergunning->getAantalExtraMetersVast();

        $vasteMeters = $metersvast[4] * 4 + $metersvast[3] * 3 + $metersvast[1];


        $perKraam = $concreetplan->getPromotieGeldenPerKraam();
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

        $perMeter = $concreetplan->getPromotieGeldenPerMeter();
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

    protected function berekenAfvaleilanden(Dagvergunning $dagvergunning, $btw) {
        $lineairplan = $this->tariefplan->getConcreetplan();

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
}