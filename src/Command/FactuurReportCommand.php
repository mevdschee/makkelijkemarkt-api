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

namespace App\Command;

use App\Entity\Factuur;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FactuurReportCommand extends ContainerAwareCommand
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('makkelijkemarkt:report:factuur');
        $this->setDescription('Generates factuur json data');
        $this->addArgument('startdate', InputArgument::REQUIRED, 'Start date yyyy-mm-dd');
        $this->addArgument('enddate', InputArgument::REQUIRED, 'End date yyyy-mm-dd');
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $marktRepo = $em->getRepository('AppApiBundle:Markt');
        $factuurRepo = $em->getRepository('AppApiBundle:Factuur');

        $out = fopen('php://output', 'w');
        fputcsv($out, array(
            'markt',
            'dagvergunningId',
            'koopmanErkenningsnummer',
            'dag',
            'voorletters',
            'achternaam',
            'productNaam',
            'productAantal',
            'productBedrag',
            'btwPerProduct',
            'totaalBtw',
            'totaalExclusief'));

        gc_enable();

        $currentDate = new \DateTime($input->getArgument('startdate'));
        $endDate = new \DateTime($input->getArgument('enddate'));

        while ($currentDate <= $endDate) {
            $markten = $marktRepo->findAll();
            foreach ($markten as $markt) {
                /**
                 * @var Factuur[] $facturen
                 */
                $facturen = $factuurRepo->getFacturenByDateRangeAndMarkt(
                    $markt,
                    $currentDate,
                    $currentDate);

                foreach ($facturen as $factuur) {
                    $dagvergunning = $factuur->getDagvergunning();
                    $koopman = $dagvergunning->getKoopman();
                    $producten = $factuur->getProducten();
                    foreach ($producten as $product) {
                        /**
                         * @var Product $product
                         */
                        fputcsv($out, array(
                            $markt->getNaam(),
                            $dagvergunning->getId(),
                            $koopman->getErkenningsnummer(),
                            $dagvergunning->getDag()->format('d-m-Y'),
                            $koopman->getVoorletters(),
                            $koopman->getAchternaam(),
                            $product->getNaam(),
                            $product->getAantal(),
                            $product->getBedrag(),
                            number_format($product->getBtwHoog() / 100 * $product->getBedrag(), 2),
                            number_format($product->getBtwHoog() / 100 * $product->getBedrag() * $product->getAantal(), 2),
                            number_format($product->getBedrag() * $product->getAantal(), 2),
                        ));
                        $em->detach($product);
                    }
                    $em->detach($factuur);
                    $em->detach($dagvergunning);
                    $em->detach($koopman);
                }
                $em->detach($markt);
            }
            $em->clear();
            gc_collect_cycles();
            $currentDate->modify('+1 day');
        }

        fclose($out);
    }
}
