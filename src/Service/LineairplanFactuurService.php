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

class LineairplanFactuurService
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
        $tariefplan = $tariefplan;
        $factuur = new Factuur();
        $factuur->setDagvergunning($dagvergunning);
        $dagvergunning->setFactuur($factuur);

        $dag = $dagvergunning->getDag();

        $btwTarief = $this->btwTariefRepository->findOneBy(array('jaar' => $dag->format('Y')));
        $btw = null !== $btwTarief ? $btwTarief->getHoog() : 0;

        list($totaalMeters, $totaalKramen) = $this->berekenMeters($dagvergunning, $tariefplan, $factuur, $btw);

        $this->berekenElektra($dagvergunning, $tariefplan, $factuur, $btw);
        $this->berekenKrachtstroom($dagvergunning, $tariefplan, $factuur,  $btw);
        $this->berekenEenmaligElektra($dagvergunning, $tariefplan, $factuur,  $btw);

        $this->berekenAfvaleilanden($dagvergunning, $tariefplan, $factuur,  $btw);

        $this->berekenPromotiegelden($dagvergunning, $tariefplan, $factuur, $totaalMeters, $totaalKramen);

        return $factuur;
    }

    protected function berekenMeters(Dagvergunning $dagvergunning, Tariefplan $tariefplan, Factuur $factuur, $btw)
    {
        $lineairplan = $tariefplan->getLineairplan();

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
            $product->setFactuur($factuur);
            $product->setAantal($totaalMetersVast);
            $product->setBtwHoog(0);
            $factuur->addProducten($product);

        }
        if ($teBetalenMeters >= 1) {
            $product = new Product();
            $product->setNaam('afgenomen meters');
            $product->setBedrag($tariefPerMeter);
            $product->setFactuur($factuur);
            $product->setAantal($teBetalenMeters);
            $product->setBtwHoog(0);
            $factuur->addProducten($product);

            $product = new Product();
            $product->setNaam('reiniging');
            $product->setBedrag($lineairplan->getReinigingPerMeter());
            $product->setFactuur($factuur);
            $product->setAantal($teBetalenMeters);
            $product->setBtwHoog($btw);
            $factuur->addProducten($product);

            $product = new Product();
            $product->setNaam('toeslag bedrijfsafval');
            $product->setBedrag($lineairplan->getToeslagBedrijfsafvalPerMeter());
            $product->setFactuur($factuur);
            $product->setAantal($teBetalenMeters);
            $product->setBtwHoog($btw);
            $factuur->addProducten($product);
        }

        return [$totaalMeters, $totaalKramen];
    }

    protected function berekenKrachtstroom(Dagvergunning $dagvergunning, Tariefplan $tariefplan, Factuur $factuur, $btw)
    {
        $lineairplan = $tariefplan->getLineairplan();

        $vast = $dagvergunning->getAantalElektraVast();
        $afname = $dagvergunning->getAantalElektra();
        $kosten = $lineairplan->getToeslagKrachtstroomPerAansluiting();
        if (null !== $kosten && $kosten > 0 && $afname >= 1 && $dagvergunning->getKrachtstroom() === true) {
            if ($vast >= 1) {
                $afname = $afname - $vast;
                $product = new Product();
                $product->setNaam('elektra krachtstroom (vast)');
                $product->setBedrag(0);
                $product->setFactuur($factuur);
                $product->setAantal($vast);
                $product->setBtwHoog(0);
                $factuur->addProducten($product);
            }
            if ($afname >= 1) {
                $product = new Product();
                $product->setNaam('elektra krachtstroom');
                $product->setBedrag($kosten);
                $product->setFactuur($factuur);
                $product->setAantal($afname);
                $product->setBtwHoog($btw);
                $factuur->addProducten($product);
            }
        }
    }

    protected function berekenElektra(Dagvergunning $dagvergunning, Tariefplan $tariefplan, Factuur $factuur, $btw)
    {
        $lineairplan = $tariefplan->getLineairplan();

        $vast = $dagvergunning->getAantalElektraVast();
        $afname = $dagvergunning->getAantalElektra();
        $kosten = $lineairplan->getElektra();
        if (null !== $kosten && $kosten > 0 && $afname >= 1 && $dagvergunning->getKrachtstroom() === false) {
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
                $product->setBtwHoog($btw);
                $factuur->addProducten($product);
            }
        }
    }

    protected function berekenAfvaleilanden(Dagvergunning $dagvergunning, Tariefplan $tariefplan, Factuur $factuur, $btw)
    {
        $lineairplan = $tariefplan->getLineairplan();

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

    protected function berekenEenmaligElektra(Dagvergunning $dagvergunning, Tariefplan $tariefplan, Factuur $factuur, $btw)
    {
        $lineairplan = $tariefplan->getLineairplan();

        $eenmaligElektra = $dagvergunning->getEenmaligElektra();
        $kosten = $lineairplan->getEenmaligElektra();
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
                $product->setBtwHoog($btw);
                $factuur->addProducten($product);
            }
        }
    }

    protected function berekenPromotiegelden(Dagvergunning $dagvergunning, Tariefplan $tariefplan, Factuur $factuur, $meters, $kramen)
    {
        $lineairplan = $tariefplan->getLineairplan();

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

        $perMeter = $lineairplan->getPromotieGeldenPerMeter();
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
}
