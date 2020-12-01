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

use App\Utils\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FactuurInfoCommand extends ContainerAwareCommand
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('makkelijkemarkt:info:factuur');
        $this->setDescription('Generates invoice info');
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new Logger();
        $logger->addOutput($output);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $factuurService = $this->getContainer()->get('appapi.factuurservice');

        $factuurRepo = $em->getRepository('AppApiBundle:Factuur');

        $facturen = $factuurRepo->findAll();

        $totalen = [];
        $i = 0;
        foreach ($facturen as $factuur) {
            $totaal = $factuurService->getTotaal($factuur);
            if ($totaal == 0) {
                continue;
            }
            $totalen[] = $totaal;
            echo ++$i . "\n";

            if ($i > 20000) {
                break;
            }
        }
        $avg = array_sum($totalen) / count($totalen);
        echo $avg;

    }
}
