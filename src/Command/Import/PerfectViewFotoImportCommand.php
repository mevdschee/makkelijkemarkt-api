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

use App\Utils\CsvIterator;
use App\Utils\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PerfectViewFotoImportCommand extends ContainerAwareCommand
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('makkelijkemarkt:import:perfectview:foto');
        $this->setDescription('Importeert een CSV bestand en foto map uit PerfectView');
        $this->addArgument('file', InputArgument::REQUIRED, 'CSV bestand met koopman informatie');
        $this->addArgument('directory', InputArgument::REQUIRED, 'Map met foto\'s');
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new Logger();
        $logger->addOutput($output);

        /* @var $process \App\Process\PerfectViewKoopmanFotoImport */
        $process = $this->getContainer()->get('import.process.perfectviewfotoimport');
        $process->setLogger($logger);

        $file = $input->getArgument('file');
        $dir = $input->getArgument('directory');
        $logger->info('PerfectView Koopman Foto Import');
        $logger->info('Start date/time', ['datetime' => date('c')]);
        $logger->info('File', ['file' => $file]);
        $content = new CsvIterator($file);
        $process->execute($content, $dir);
        $logger->info('Import done', ['datetime' => date('c')]);
    }
}
