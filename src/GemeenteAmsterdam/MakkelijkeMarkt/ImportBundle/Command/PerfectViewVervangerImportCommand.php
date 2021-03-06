<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Process\PerfectViewMarktImport;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Utils\Logger;
use GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Utils\CsvIterator;

class PerfectViewVervangerImportCommand extends ContainerAwareCommand
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('makkelijkemarkt:import:perfectview:vervanger');
        $this->setDescription('Importeert een CSV bestand uit PerfectView met vervanger informatie');
        $this->addArgument('file', InputArgument::REQUIRED, 'CSV bestand met vervanger informatie');
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new Logger();
        $logger->addOutput($output);

        /* @var $process \GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Process\PerfectViewVervangerImport */
        $process = $this->getContainer()->get('import.process.perfectviewvervangerimport');
        $process->setLogger($logger);

        $file = $input->getArgument('file');
        $logger->info('PerfectView Vervanger Import');
        $logger->info('Start date/time', ['datetime' => date('c')]);
        $logger->info('File', ['file' => $file]);
        $content = new CsvIterator($file);
        $process->execute($content);
        $logger->info('Import done', ['datetime' => date('c')]);
    }
}