<?php

namespace App\Process;

use App\Service\FactuurService;
use Doctrine\ORM\EntityManagerInterface;
use GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Utils\Logger;

class RepairFactuur
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var FactuurService
     */
    protected $factuurService;

    /**
     * RepairFactuur constructor.
     * @param EntityManagerInterface $em
     * @param FactuurService $factuurService
     */
    public function __construct(EntityManagerInterface $em, FactuurService $factuurService)
    {
        $this->em = $em;
        $this->factuurService = $factuurService;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function run()
    {
        $this->logger->info('RepairFactuur:run');

        $dagvergunningRepo = $this->em->getRepository('AppApiBundle:Dagvergunning');

        $datum = new \DateTime('2015-12-30 00:00:00');

        $dagvergunningen = $dagvergunningRepo->findBy(array(
            'factuur' => null,
            'doorgehaald' => false,
        ));

        $i = 0;
        foreach ($dagvergunningen as $dagvergunning) {
            if ($datum <= $dagvergunning->getAanmaakDatumtijd()) {
                $ts = microtime(true);
                $this->logger->info('Processing', array('id' => $dagvergunning->getId()));
                $factuur = $this->factuurService->createFactuur($dagvergunning);
                $this->logger->info('Create factuur', array('id' => $dagvergunning->getId(), 'seconds' => microtime(true) - $ts));
                if (null !== $factuur) {
                    $this->factuurService->saveFactuur($factuur);
                }
                $this->logger->info('Save factuur', array('id' => $dagvergunning->getId(), 'seconds' => microtime(true) - $ts));

                $i++;
                $te = microtime(true);
                $this->logger->info('Finished', array('id' => $dagvergunning->getId(), 'seconds' => $te - $ts));
            }
        }

        $this->logger->info('Count', array('count' => $i));
    }
}
