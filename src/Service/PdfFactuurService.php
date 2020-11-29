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

use Qipsius\TCPDFBundle\Controller\TCPDFController;

class PdfFactuurService
{
    protected $fontname;

    protected $fontnameBold;

    protected $factuurService;

    public function __construct(FactuurService $factuurService)
    {
        $this->factuurService = $factuurService;

        $this->fontname = 'dejavusans';
        $this->fontnameBold = 'dejavusansb';

        // should we condtionally add fonts? (bundle build step)
        // are we licensed to distribute?
        //
        // $this->fontname = \TCPDF_FONTS::addTTFfont(
        //     getcwd() . '/src/GemeenteAmsterdam/MakkelijkeMarkt/AppApiBundle/Resources/public/fonts/Avenir-Roman.ttf',
        //     'TrueTypeUnicode',
        //     '',
        //     96
        // );
        // $this->fontnameBold = \TCPDF_FONTS::addTTFfont(
        //     getcwd() . '/src/GemeenteAmsterdam/MakkelijkeMarkt/AppApiBundle/Resources/public/fonts/Avenir-Heavy.ttf',
        //     'TrueTypeUnicode',
        //     '',
        //     96
        // );
    }

    public function generate(TCPDFController $tcpdf, $koopman, $dagvergunningen)
    {
        $pdf = $tcpdf->create();

        // set document information
        $pdf->SetCreator('Gemeente Amsterdam');
        $pdf->SetAuthor('Gemeente Amsterdam');
        $pdf->SetTitle('Factuur');
        $pdf->SetSubject('Factuur');
        $pdf->SetKeywords('Factuur');

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetAutoPageBreak(false, 0);

        foreach ($dagvergunningen as $vergunning) {
            if ($vergunning->getFactuur() !== null) {
                $this->addVergunning($pdf, $koopman, $vergunning);
            }
        }

        return $pdf;
    }

    protected function addVergunning(\TCPDF $pdf, $koopman, $vergunning)
    {
        $pdf->AddPage();
        $pdf->Image(
            getcwd() . '/src/GemeenteAmsterdam/MakkelijkeMarkt/AppApiBundle/Resources/public/images/GASD_1.png',
            10,
            10,
            50
        );

        $pdf->Ln(40);

        $pdf->SetFont($this->fontname, 'b', 8);
        $pdf->Cell(16, 6, '', 0, 0);
        $pdf->Cell(164, 6, '', 0, 0);

        $pdf->Ln(10);

        $pdf->SetFont($this->fontname, 'b', 11);
        $pdf->Cell(16, 6, '', 0, 0);
        $pdf->Cell(164, 6, $koopman->getVoorletters() . ' ' . $koopman->getTussenvoegsels() . ' ' . $koopman->getAchternaam(), 0, 1);

        $pdf->SetY(10);

        $pdf->SetFont($this->fontname, 'b', 10);
        $pdf->Cell(130, 6, '', 0, 0);
        $pdf->Cell(50, 6, 'Stadswerken', 0, 0);
        $pdf->Ln(5);
        $pdf->Cell(130, 6, '', 0, 0);
        $pdf->Cell(50, 6, 'Bezoekadres', 0, 0);
        $pdf->Ln(5);
        $pdf->Cell(130, 6, '', 0, 0);
        $pdf->Cell(50, 6, 'Amstel 1', 0, 0);
        $pdf->Ln(5);
        $pdf->Cell(130, 6, '', 0, 0);
        $pdf->Cell(50, 6, '1011 PN Amsterdam', 0, 0);

        $pdf->Ln(10);
        $pdf->Cell(130, 6, '', 0, 0);
        $pdf->Cell(50, 6, 'Postbus 202', 0, 0);
        $pdf->Ln(5);
        $pdf->Cell(130, 6, '', 0, 0);
        $pdf->Cell(50, 6, '1000 AE Amsterdam', 0, 0);
        $pdf->Ln(5);
        $pdf->Cell(130, 6, '', 0, 0);
        $pdf->Cell(50, 6, 'Telefoon 020 2552912', 0, 0);
        $pdf->Ln(5);
        $pdf->Cell(130, 6, '', 0, 0);
        $pdf->Cell(50, 6, 'Bereikbaar van 8.00-18.00', 0, 0);
        $pdf->Ln(5);
        $pdf->Cell(130, 6, '', 0, 0);
        $pdf->Cell(50, 6, 'Email', 0, 0);
        $pdf->Ln(5);
        $pdf->Cell(130, 6, '', 0, 0);
        $pdf->Cell(50, 6, 'debiteurenadministratie@amsterdam.nl', 0, 0);

        $pdf->Ln(5);
        $pdf->Cell(130, 6, '', 0, 0);
        $pdf->Cell(50, 6, 'BTW nr NL002564440B01', 0, 0);
        $pdf->Ln(5);
        $pdf->Cell(130, 6, '', 0, 0);
        $pdf->Cell(50, 6, 'KvK nr 34366966 0000', 0, 0);

        $pdf->Ln(10);

        $pdf->Cell(16, 6, '', 0, 0);
        $pdf->SetFont($this->fontnameBold, 'b', 9);
        $pdf->Cell(26, 6, 'Factuurnummer', 0, 0);
        $pdf->SetFont($this->fontname, 'b', 9);
        $pdf->Cell(26, 6, 'mm' . $vergunning->getFactuur()->getId(), 0, 0);
        $pdf->SetFont($this->fontnameBold, 'b', 9);
        $pdf->Cell(26, 6, 'Factuurdatum', 0, 0);
        $pdf->SetFont($this->fontname, 'b', 9);
        $dag = implode('-', array_reverse(explode('-', $vergunning->getDag()->format('d-m-Y'))));
        $pdf->Cell(26, 6, $dag, 0, 1);

        $pdf->Cell(16, 6, '', 0, 0);
        $pdf->SetFont($this->fontnameBold, 'b', 9);
        $pdf->Cell(144, 6, 'Omschrijving', 'B', 0);
        $pdf->Cell(20, 6, 'Bedrag €', 'B', 1, 'R');

        $pdf->SetFont($this->fontname, 'b', 9);

        $pdf->Cell(16, 6, '', 0, 0);
        $pdf->Cell(164, 6, 'Markt: ' . $vergunning->getMarkt()->getNaam(), '', 1);

        $btwTotaal = [];
        $btwOver = [];

        foreach ($vergunning->getFactuur()->getProducten() as $product) {
            $pdf->Cell(16, 6, '', 0, 0);
            $btwText = $product->getBtwHoog() > 0 ? '. excl. ' . $product->getBtwHoog() . '% BTW' : '';
            $pdf->Cell(144, 6, $product->getAantal() . ' maal ' . $product->getNaam() . $btwText, '', 0);
            $pdf->Cell(20, 6, number_format($product->getAantal() * $product->getBedrag(), 2), 0, 0, 'R');
            if (!isset($btwTotaal[$product->getBtwHoog()])) {
                $btwTotaal[$product->getBtwHoog()] = 0;
                $btwOver[$product->getBtwHoog()] = 0;
            }

            $btwTotaal[$product->getBtwHoog()] += number_format($product->getAantal() * $product->getBedrag() * ($product->getBtwHoog() / 100), 2);
            $btwOver[$product->getBtwHoog()] += number_format($product->getAantal() * $product->getBedrag(), 2);

            $pdf->Ln(5);
        }

        $pdf->Ln(5);

        $pdf->Cell(98, 6, '', 0, 0);
        $pdf->Cell(41, 6, 'Subtotaal', 'T', 0);
        $pdf->Cell(41, 6, $this->factuurService->getTotaal($vergunning->getFactuur(), false), 'T', 0, 'R');
        $pdf->Ln(5);
        foreach ($btwTotaal as $key => $value) {
            $pdf->Cell(98, 6, '', 0, 0);
            $pdf->Cell(41, 6, 'BTW ' . $key . '% over ' . number_format($btwOver[$key], 2), 0, 0);
            $pdf->Cell(41, 6, number_format($value, 2), 0, 0, 'R');
            $pdf->Ln(5);
        }

        $pdf->SetFont($this->fontnameBold, 'b', 9);
        $pdf->Cell(98, 6, '', 0, 0);
        $pdf->Cell(41, 6, 'Totaal', 'T', 0);
        $pdf->Cell(41, 6, $this->factuurService->getTotaal($vergunning->getFactuur()), 'T', 0, 'R');

    }
}
