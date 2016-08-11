<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Utils\Logger;


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