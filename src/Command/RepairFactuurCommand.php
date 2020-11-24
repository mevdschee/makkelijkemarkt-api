<?php

namespace App\Command;

use GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Utils\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RepairFactuurCommand extends ContainerAwareCommand
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('makkelijkemarkt:repair:factuur');
        $this->setDescription('Generates missing invoices');
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new Logger();
        $logger->addOutput($output);

        $repairFactuur = $this->getContainer()->get('appapi.repair.factuur');
        $repairFactuur->setLogger($logger);

        $repairFactuur->run();
    }
}
