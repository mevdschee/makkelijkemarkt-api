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

namespace App\Service;

use App\Entity\Dagvergunning;
use App\Entity\Factuur;
use App\Entity\Product;
use App\Entity\Sollicitatie;
use App\Entity\Tariefplan;
use App\Repository\BtwTariefRepository;

class ConcreetplanFactuurService
{

    /**
     * @var BtwTariefRepository
     */
    private $btwTariefRepository;

    public function __construct(BtwTariefRepository $btwTariefRepository)
    {
        $this->btwTariefRepository = $btwTariefRepository;
    }

    /**
     * @param Dagvergunning $dagvergunning
     * @param Tariefplan $tariefplan
     * @return Factuur|null
     */
    public function createFactuur(Dagvergunning $dagvergunning, Tariefplan $tariefplan)
    {
        $factuur = new Factuur();
        $factuur->setDagvergunning($dagvergunning);
        $dagvergunning->setFactuur($factuur);

        list($totaalMeters, $totaalKramen) = $this->berekenMeters($dagvergunning, $tariefplan, $factuur);

        $this->berekenElektra($dagvergunning, $tariefplan, $factuur);

        $this->berekenEenmaligElektra($dagvergunning, $tariefplan, $factuur);

        $this->berekenPromotiegelden($dagvergunning, $tariefplan, $factuur, $totaalMeters, $totaalKramen);

        $dag = $dagvergunning->getDag();

        $btwTarief = $this->btwTariefRepository->findOneBy(array('jaar' => $dag->format('Y')));
        $btw = null !== $btwTarief ? $btwTarief->getHoog() : 0;

        $this->berekenAfvaleilanden($dagvergunning, $tariefplan, $factuur, $btw);

        return $factuur;
    }

    protected function berekenMeters(Dagvergunning $dagvergunning, Tariefplan $tariefplan, Factuur $factuur)
    {
        $concreetplan = $tariefplan->getConcreetplan();

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
            $facturabeleMeters = 0;
            while ($meters[4] >= 1) {
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
                $product->setFactuur($factuur);
                $product->setAantal($facturabeleMeters);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
            if ($nietFacturabeleMeters >= 1) {
                $product = new Product();
                $product->setNaam('4 meter plaats');
                $product->setBedrag($vierMeter);
                $product->setFactuur($factuur);
                $product->setAantal($nietFacturabeleMeters);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
        }

        $drieMeter = $concreetplan->getDrieMeter();
        if (null !== $drieMeter && $drieMeter > 0 && $meters[3] >= 1) {
            $nietFacturabeleMeters = 0;
            $facturabeleMeters = 0;
            while ($meters[3] >= 1) {
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
                $product->setFactuur($factuur);
                $product->setAantal($facturabeleMeters);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
            if ($nietFacturabeleMeters >= 1) {
                $product = new Product();
                $product->setNaam('3 meter plaats');
                $product->setBedrag($drieMeter);
                $product->setFactuur($factuur);
                $product->setAantal($nietFacturabeleMeters);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
        }

        $eenMeter = $concreetplan->getEenMeter();
        if (null !== $eenMeter && $eenMeter > 0 && $meters[1] >= 1) {
            $nietFacturabeleMeters = 0;
            $facturabeleMeters = 0;
            while ($meters[1] >= 1) {
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
                $product->setFactuur($factuur);
                $product->setAantal($facturabeleMeters);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
            if ($nietFacturabeleMeters >= 1) {
                $product = new Product();
                $product->setNaam('extra meter');
                $product->setBedrag($eenMeter);
                $product->setFactuur($factuur);
                $product->setAantal($nietFacturabeleMeters);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
        }

        return [$totaalMeters, $totaalKramen];
    }

    protected function berekenElektra(Dagvergunning $dagvergunning, Tariefplan $tariefplan, Factuur $factuur)
    {
        $concreetplan = $tariefplan->getConcreetplan();

        $vast = $dagvergunning->getAantalElektraVast();
        $afname = $dagvergunning->getAantalElektra();
        $kosten = $concreetplan->getElektra();
        if (null !== $kosten && $kosten > 0 && $afname >= 1) {
            if ($vast >= 1) {
                $afname = $afname - $vast;
                $product = new Product();
                $product->setNaam('elektra (vast)');
                $product->setBedrag(0);
                $product->setFactuur($factuur);
                $product->setAantal($vast);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
            if ($afname >= 1) {
                $product = new Product();
                $product->setNaam('elektra');
                $product->setBedrag($kosten);
                $product->setFactuur($factuur);
                $product->setAantal($afname);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
        }
    }

    protected function berekenEenmaligElektra(Dagvergunning $dagvergunning, Tariefplan $tariefplan, Factuur $factuur)
    {
        $concreetplan = $tariefplan->getConcreetplan();

        $eenmaligElektra = $dagvergunning->getEenmaligElektra();
        $kosten = $concreetplan->getEenmaligElektra();
        if (null !== $kosten && $kosten > 0 && true === $eenmaligElektra) {
            if (in_array($dagvergunning->getStatusSolliciatie(), array(Sollicitatie::STATUS_VKK, Sollicitatie::STATUS_VPL))) {
                $product = new Product();
                $product->setNaam('eenmalige elektra (vast)');
                $product->setBedrag(0);
                $product->setFactuur($factuur);
                $product->setAantal(1);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            } else {
                $product = new Product();
                $product->setNaam('eenmalige elektra');
                $product->setBedrag($kosten);
                $product->setFactuur($factuur);
                $product->setAantal(1);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
        }
    }

    protected function berekenPromotiegelden(Dagvergunning $dagvergunning, Tariefplan $tariefplan, Factuur $factuur, $meters, $kramen)
    {
        $concreetplan = $tariefplan->getConcreetplan();

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
                $product->setFactuur($factuur);
                $product->setAantal($kramen);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            } else {
                $product = new Product();
                $product->setNaam('promotiegelden per koopman');
                $product->setBedrag($perKraam);
                $product->setFactuur($factuur);
                $product->setAantal($kramen);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
        }

        $perMeter = $concreetplan->getPromotieGeldenPerMeter();
        if (null !== $perMeter && $perMeter > 0 && $meters > 0) {
            if ($vasteMeters >= 1) {
                $meters = $meters - $vasteMeters;
                $product = new Product();
                $product->setNaam('promotiegelden per meter (vast)');
                $product->setBedrag(0);
                $product->setFactuur($factuur);
                $product->setAantal($vasteMeters);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
            if ($meters >= 1) {
                $product = new Product();
                $product->setNaam('promotiegelden per meter');
                $product->setBedrag($perMeter);
                $product->setFactuur($factuur);
                $product->setAantal($meters);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
        }
    }

    protected function berekenAfvaleilanden(Dagvergunning $dagvergunning, Tariefplan $tariefplan, Factuur $factuur, $btw)
    {
        $lineairplan = $tariefplan->getConcreetplan();

        $vast = $dagvergunning->getAfvaleilandVast();
        $afname = $dagvergunning->getAfvaleiland();
        $kosten = $lineairplan->getAfvaleiland();
        if (null !== $kosten && $kosten > 0 && $afname >= 1) {
            if ($vast >= 1) {
                $afname = $afname - $vast;
                $product = new Product();
                $product->setNaam('afvaleiland (vast)');
                $product->setBedrag(0);
                $product->setFactuur($factuur);
                $product->setAantal($vast);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
            if ($afname >= 1) {
                $product = new Product();
                $product->setNaam('afvaleiland');
                $product->setBedrag($kosten);
                $product->setFactuur($factuur);
                $product->setAantal($afname);
                $product->setBtwHoog($btw);
                $factuur->addProducten($product);
            }
        }
    }
}
