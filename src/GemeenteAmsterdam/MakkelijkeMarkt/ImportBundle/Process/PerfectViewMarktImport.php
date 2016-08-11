<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Process;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\MarktRepository;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\MarktExtraDataRepository;
use Doctrine\ORM\EntityManager;
use GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Utils\Logger;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class PerfectViewMarktImport
{
    /**
     * @var MarktRepository
     */
    protected $marktRepository;

    /**
     * @var MarktExtraDataRepository
     */
    protected $marktExtraDataRepository;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var array
     */
    protected $soortMarkConversion = [
        'Dag' => Markt::SOORT_DAG,
        'Week' => Markt::SOORT_WEEK,
        'Seizoen' => Markt::SOORT_SEIZOEN,
    ];

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param MarktRepository $marktRepository
     * @param MarktExtraDataRepository $marktExtraDataRepository
     */
    public function __construct(MarktRepository $marktRepository, MarktExtraDataRepository $marktExtraDataRepository, EntityManager $em)
    {
        $this->marktRepository = $marktRepository;
        $this->marktExtraDataRepository = $marktExtraDataRepository;
        $this->em = $em;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array $perfectViewRecords
     */
    public function execute($perfectViewRecords)
    {
        $headings = $perfectViewRecords->getHeadings();
        $requiredHeadings = ['KAARTNR', 'AFKORTING', 'MARKTNAAM', 'SOORT_MARK', 'A1_METER', 'A3_METER', 'A4_METER', 'ELEKTRA', 'KRACHTROOM'];
        foreach ($requiredHeadings as $requiredHeading) {
            if (in_array($requiredHeading, $headings) === false) {
                throw new \RuntimeException('Missing column "' . $requiredHeading . '" in import file');
            }
        }

        foreach ($perfectViewRecords as $pvRecord)
        {
            // skip empty records
            if ($pvRecord === null || $pvRecord === '') {
                $this->logger->info('Skip, record is empty');
                continue;
            }

            $this->logger->info('PerfectView record import', ['kaartnr' => $pvRecord['KAARTNR']]);
            $markt = $this->marktRepository->getByPerfectViewNummer($pvRecord['KAARTNR']);

            // create new markt
            if ($markt === null)
            {
                $this->logger->info('Nieuwe markt, aanmaken in database', ['kaartnr' => $pvRecord['KAARTNR']]);
                $markt = new Markt();
                $markt->setPerfectViewNummer($pvRecord['KAARTNR']);
                $this->em->persist($markt);
            }
            else
            {
                $this->logger->info('Bestaande markt, bijwerken in database', ['kaartnr' => $pvRecord['KAARTNR'], 'id' => $markt->getId()]);
            }

            // update markt
            $markt->setAfkorting($pvRecord['AFKORTING']);
            $markt->setNaam($pvRecord['MARKTNAAM']);
            $markt->setSoort($this->soortMarkConversion[$pvRecord['SOORT_MARK']]);
            $markt->setExtraMetersMogelijk($pvRecord['A1_METER'] === 'True');
            $markt->setStandaardKraamAfmeting((($pvRecord['A3_METER'] === 'True') ? 3 : ( ($pvRecord['A4_METER'] === 'True') ? 4 : 0 )));

            $opties = [];
            if ($pvRecord['A3_METER'] === 'True' || $pvRecord['A3_METER'] === 'Waar')
                $opties[] = '3mKramen';
            if ($pvRecord['A4_METER'] === 'True' || $pvRecord['A4_METER'] === 'Waar')
                $opties[] = '4mKramen';
            if ($pvRecord['A1_METER'] === 'True' || $pvRecord['A1_METER'] === 'Waar')
                $opties[] = 'extraMeters';
            if ($pvRecord['KRACHTROOM'] === 'True' || $pvRecord['KRACHTROOM'] === 'Waar')
                $opties[] = 'elektra';
            if ($pvRecord['AFVAL'] === 'True' || $pvRecord['AFVAL'] === 'Waar')
                $opties[] = 'afvaleiland';
            /** TODO: Zorg dat deze optie in perfectview gedefineerd wordt */
            if ($pvRecord['AFKORTING'] === 'PEK')
                $opties[] = 'eenmaligElektra';
            /** End fix */
            $markt->setAanwezigeOpties($opties);

            // load additional data
            $marktExtraData = $this->marktExtraDataRepository->getByPerfectViewNumber($pvRecord['KAARTNR']);

            // if extra data found, attach it
            if ($marktExtraData !== null)
            {
                $this->logger->info('Extra marktdata gevonden', ['kaartnr' => $pvRecord['KAARTNR']]);
                $markt->setGeoArea($marktExtraData->getGeoArea());
                $markt->setMarktdagen($marktExtraData->getMarktdagen());
                $markt->setAanwezigeOpties(array_merge($opties, $marktExtraData->getAanwezigeOpties()));
            }
        }

        $this->em->flush();
    }
}