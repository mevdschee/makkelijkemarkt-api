<?php
/*
 *  Copyright (C) 2017 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class PdfFactuurService
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \TCPDF $pdf
     */
    protected $pdf;

    protected $fontname;

    protected $fontnameBold;

    protected $factuurService;

    public function __construct($container)
    {
        $this->container = $container;
        $this->factuurService = $container->get('appapi.factuurservice');

        $this->fontname = \TCPDF_FONTS::addTTFfont(
            getcwd() . '/src/GemeenteAmsterdam/MakkelijkeMarkt/AppApiBundle/Resources/public/fonts/Avenir-Roman.ttf',
            'TrueTypeUnicode',
            '',
            96
        );
        $this->fontnameBold = \TCPDF_FONTS::addTTFfont(
            getcwd() . '/src/GemeenteAmsterdam/MakkelijkeMarkt/AppApiBundle/Resources/public/fonts/Avenir-Heavy.ttf',
            'TrueTypeUnicode',
            '',
            96
        );
    }

    public function generate($koopman, $dagvergunningen) {
        $this->pdf = $this->container->get("white_october.tcpdf")->create();

        // set document information
        $this->pdf->SetCreator('Gemeente Amsterdam');
        $this->pdf->SetAuthor('Gemeente Amsterdam');
        $this->pdf->SetTitle('Factuur');
        $this->pdf->SetSubject('Factuur');
        $this->pdf->SetKeywords('Factuur');

        $this->pdf->SetPrintHeader(false);
        $this->pdf->SetPrintFooter(false);
        $this->pdf->SetAutoPageBreak(false, 0);

        foreach ($dagvergunningen as $vergunning) {
            if ($vergunning->getFactuur() !== null) {
                $this->addVergunning($koopman, $vergunning);
            }
        }

        return $this->pdf;
    }

    protected function addVergunning($koopman, $vergunning) {
        $this->pdf->AddPage();
        $this->pdf->Image(
            getcwd() . '/src/GemeenteAmsterdam/MakkelijkeMarkt/AppApiBundle/Resources/public/images/GASD_1.png',
            10,
            10,
            50
        );

        $this->pdf->Ln(40);

        $this->pdf->SetFont($this->fontname, 'b', 8);
        $this->pdf->Cell(16, 6, '', 0, 0);
        $this->pdf->Cell(164, 6, '', 0, 0);

        $this->pdf->Ln(10);

        $this->pdf->SetFont($this->fontname, 'b', 11);
        $this->pdf->Cell(16, 6, '', 0, 0);
        $this->pdf->Cell(164, 6, $koopman->getVoorletters() . ' ' . $koopman->getTussenvoegsels() . ' ' . $koopman->getAchternaam(), 0, 1);

        $this->pdf->SetY(10);

        $this->pdf->SetFont($this->fontname, 'b', 10);
        $this->pdf->Cell(130, 6, '', 0, 0);
        $this->pdf->Cell(50, 6, 'Stadswerken', 0, 0);
        $this->pdf->Ln(5);
        $this->pdf->Cell(130, 6, '', 0, 0);
        $this->pdf->Cell(50, 6, 'Bezoekadres', 0, 0);
        $this->pdf->Ln(5);
        $this->pdf->Cell(130, 6, '', 0, 0);
        $this->pdf->Cell(50, 6, 'Amstel 1', 0, 0);
        $this->pdf->Ln(5);
        $this->pdf->Cell(130, 6, '', 0, 0);
        $this->pdf->Cell(50, 6, '1011 PN Amsterdam', 0, 0);

        $this->pdf->Ln(10);
        $this->pdf->Cell(130, 6, '', 0, 0);
        $this->pdf->Cell(50, 6, 'Postbus 202', 0, 0);
        $this->pdf->Ln(5);
        $this->pdf->Cell(130, 6, '', 0, 0);
        $this->pdf->Cell(50, 6, '1000 AE Amsterdam', 0, 0);
        $this->pdf->Ln(5);
        $this->pdf->Cell(130, 6, '', 0, 0);
        $this->pdf->Cell(50, 6, 'Telefoon 020 2552912', 0, 0);
        $this->pdf->Ln(5);
        $this->pdf->Cell(130, 6, '', 0, 0);
        $this->pdf->Cell(50, 6, 'Bereikbaar van 8.00-18.00', 0, 0);
        $this->pdf->Ln(5);
        $this->pdf->Cell(130, 6, '', 0, 0);
        $this->pdf->Cell(50, 6, 'Email', 0, 0);
        $this->pdf->Ln(5);
        $this->pdf->Cell(130, 6, '', 0, 0);
        $this->pdf->Cell(50, 6, 'debiteurenadministratie@amsterdam.nl', 0, 0);

        $this->pdf->Ln(5);
        $this->pdf->Cell(130, 6, '', 0, 0);
        $this->pdf->Cell(50, 6, 'BTW nr NL002564440B01', 0, 0);
        $this->pdf->Ln(5);
        $this->pdf->Cell(130, 6, '', 0, 0);
        $this->pdf->Cell(50, 6, 'KvK nr 34366966 0000', 0, 0);

        $this->pdf->Ln(10);

        $this->pdf->Cell(16, 6, '', 0, 0);
        $this->pdf->SetFont($this->fontnameBold, 'b', 9);
        $this->pdf->Cell(26, 6, 'Factuurnummer', 0, 0);
        $this->pdf->SetFont($this->fontname, 'b', 9);
        $this->pdf->Cell(26, 6, 'mm' . $vergunning->getFactuur()->getId(), 0, 0);
        $this->pdf->SetFont($this->fontnameBold, 'b', 9);
        $this->pdf->Cell(26, 6, 'Factuurdatum', 0, 0);
        $this->pdf->SetFont($this->fontname, 'b', 9);
        $dag = implode('-',array_reverse(explode('-',$vergunning->getDag()->format('d-m-Y'))));
        $this->pdf->Cell(26, 6, $dag, 0, 1);

        $this->pdf->Cell(16, 6, '', 0, 0);
        $this->pdf->SetFont($this->fontnameBold, 'b', 9);
        $this->pdf->Cell(144, 6, 'Omschrijving', 'B', 0);
        $this->pdf->Cell(20, 6, 'Bedrag €', 'B', 1, 'R');

        $this->pdf->SetFont($this->fontname, 'b', 9);

        $this->pdf->Cell(16, 6, '', 0, 0);
        $this->pdf->Cell(164, 6, 'Markt: ' . $vergunning->getMarkt()->getNaam(), '', 1);


        $btwTotaal = [];
        $btwOver   = [];

        foreach ($vergunning->getFactuur()->getProducten() as $product) {
            $this->pdf->Cell(16, 6, '', 0, 0);
            $btwText = $product->getBtwHoog() > 0 ? '. excl. ' . $product->getBtwHoog() . '% BTW' : '';
            $this->pdf->Cell(144, 6, $product->getAantal() . ' maal ' . $product->getNaam() . $btwText , '', 0);
            $this->pdf->Cell(20, 6, number_format($product->getAantal() * $product->getBedrag(),2), 0, 0, 'R');
            if (!isset($btwTotaal[$product->getBtwHoog()])) {
                $btwTotaal[$product->getBtwHoog()] = 0;
                $btwOver[$product->getBtwHoog()] = 0;
            }

            $btwTotaal[$product->getBtwHoog()] += number_format($product->getAantal() * $product->getBedrag() * ($product->getBtwHoog() / 100),2);
            $btwOver[$product->getBtwHoog()] += number_format($product->getAantal() * $product->getBedrag(),2);

            $this->pdf->Ln(5);
        }

        $this->pdf->Ln(5);

        $this->pdf->Cell(98, 6, '', 0, 0);
        $this->pdf->Cell(41, 6, 'Subtotaal', 'T', 0);
        $this->pdf->Cell(41, 6, $this->factuurService->getTotaal($vergunning->getFactuur(), false), 'T', 0, 'R');
        $this->pdf->Ln(5);
        foreach ($btwTotaal as $key => $value) {
            $this->pdf->Cell(98, 6, '', 0, 0);
            $this->pdf->Cell(41, 6, 'BTW ' . $key . '% over ' . number_format($btwOver[$key],2), 0, 0);
            $this->pdf->Cell(41, 6, number_format($value,2), 0, 0, 'R');
            $this->pdf->Ln(5);
        }

        $this->pdf->SetFont($this->fontnameBold, 'b', 9);
        $this->pdf->Cell(98, 6, '', 0, 0);
        $this->pdf->Cell(41, 6, 'Totaal', 'T', 0);
        $this->pdf->Cell(41, 6, $this->factuurService->getTotaal($vergunning->getFactuur()), 'T', 0, 'R');




    }
}