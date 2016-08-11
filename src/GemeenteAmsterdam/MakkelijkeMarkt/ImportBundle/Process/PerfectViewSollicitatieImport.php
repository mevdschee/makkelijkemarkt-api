<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Process;

use Doctrine\DBAL\Connection;

use GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Utils\Logger;
use Doctrine\DBAL\Query\QueryBuilder;
use GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Utils\CsvIterator;
use Doctrine\DBAL\Driver\PDOStatement;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Sollicitatie;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class PerfectViewSollicitatieImport
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $conn;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $markten;

    /**
     * @param \Doctrine\DBAL\Connection $conn
     */
    public function __construct(\Doctrine\DBAL\Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array $perfectViewData
     */
    public function execute(CsvIterator $content)
    {
        $headings = $content->getHeadings();
        $requiredHeadings = ['Kaartnr', 'Marktnaam', 'Erkenningsnummer', 'SollicitantenNummer', 'MarktStatus', 'PreDoorhaalStatus', 'Aantal3', 'Aantal4', 'Aantal1', 'Aantelek', 'Krachtstroom', 'DoorHaalReden', 'PLTSNR1', 'PLTSNR2', 'PLTSNR3', 'PLTSNR4', 'PLTSNR5', 'PLTSNR6', 'PLTSNR7', 'PLTSNR8'];
        foreach ($requiredHeadings as $requiredHeading) {
            if (in_array($requiredHeading, $headings) === false) {
                throw new \RuntimeException('Missing column "' . $requiredHeading . '" in import file');
            }
        }

        // iterate the csv-file
        foreach ($content as $pvRecord) {

            // skip empty records
            if ($pvRecord === null || $pvRecord === '') {
                $this->logger->info('Skip, record is empty');
                continue;
            }
            $this->logger->info('Handle PerfectView record', ['Kaartnr' => $pvRecord['Kaartnr'], 'Marktnaam' => $pvRecord['Marktnaam'], 'Erkenningsnummer' => $pvRecord['Erkenningsnummer']]);

            // get relation fields
            $markt = $this->getMarktRecord($pvRecord['Marktnaam']);
            if ($markt === null) {
                $this->logger->warning('Skip record, MARKT not found in database', ['Kaartnr' => $pvRecord['Kaartnr'], 'Marktnaam' => $pvRecord['Marktnaam']]);
                continue;
            }
            $koopman = $this->getKoopmanRecord($pvRecord['Erkenningsnummer']);
            if ($koopman === null) {
                $this->logger->warning('Skip record, KOOPMAN not found in database', ['Kaartnr' => $pvRecord['Kaartnr'], 'Erkenningsnummer' => $pvRecord['Erkenningsnummer']]);
                continue;
            }
            if ($pvRecord['SollicitantenNummer'] === '' || $pvRecord['SollicitantenNummer'] === null) {
                $this->logger->warning('Skip record, SollicitantenNummer-nummer is empty');
                continue;
            }
            if ($pvRecord['Erkenningsnummer'] === '' || $pvRecord['Erkenningsnummer'] === null) {
                $this->logger->warning('Skip record, Erkenningsnummer is empty');
                continue;
            }

            // get the record from the database (if it is already in the database)
            $solliciatieRecord = $this->getSolliciatieRecord($pvRecord['Kaartnr']);
            // prepare query builder
            $qb = $this->conn->createQueryBuilder();

            if (($solliciatieRecord !== null)) {
                // update
                $this->logger->info('Record found in database', ['id' => $solliciatieRecord['id']]);
                $qb->update('sollicitatie', 'e');
                $qb->where('e.id = :id')->setParameter('id', $solliciatieRecord['id']);
            } else {
                // insert
                $this->logger->info('Record not in database, create new');
                $qb->insert('sollicitatie');
                $qb->setValue('id', 'NEXTVAL(\'sollicitatie_id_seq\')'); // IMPORTANT setValue on Query Builder, not via helper!
            }

            // set data
            $this->setValue($qb, 'markt_id',                \PDO::PARAM_INT,  $markt['id']);
            $this->setValue($qb, 'koopman_id',              \PDO::PARAM_INT,  $koopman['id']);
            $this->setValue($qb, 'sollicitatie_nummer',     \PDO::PARAM_INT,  $pvRecord['SollicitantenNummer']);
            $this->setValue($qb, 'status',                  \PDO::PARAM_STR,  $this->convertMarktstatus((($pvRecord['MarktStatus'] === 'Doorgehaald') ? $pvRecord['PreDoorhaalStatus'] : $pvRecord['MarktStatus'])));
            $this->setValue($qb, 'vaste_plaatsen',          \PDO::PARAM_STR,  implode(',', $this->getVastePlaatsenArray($pvRecord)));
            $this->setValue($qb, 'aantal_3meter_kramen',    \PDO::PARAM_INT,  intval($pvRecord['Aantal3']));
            $this->setValue($qb, 'aantal_4meter_kramen',    \PDO::PARAM_INT,  intval($pvRecord['Aantal4']));
            $this->setValue($qb, 'aantal_extra_meters',     \PDO::PARAM_INT,  intval($pvRecord['Aantal1']));
            $this->setValue($qb, 'aantal_elektra',          \PDO::PARAM_INT,  intval($pvRecord['Aantelek']));
            $this->setValue($qb, 'aantal_afvaleilanden',    \PDO::PARAM_INT,  intval($pvRecord['AANTAFV']));
            $this->setValue($qb, 'krachtstroom',            \PDO::PARAM_BOOL, in_array($pvRecord['Krachtstroom'], ['True', '1', 1]));
            $this->setValue($qb, 'inschrijf_datum',         \PDO::PARAM_STR,  $this->convertToDateTimeString($pvRecord['Inschrijfdatum'] . ' ' . $pvRecord['Inschrijftijd']));
            $this->setValue($qb, 'doorgehaald',             \PDO::PARAM_BOOL, $pvRecord['MarktStatus'] === 'Doorgehaald');
            $this->setValue($qb, 'doorgehaald_reden',       \PDO::PARAM_STR,  $pvRecord['DoorHaalReden']);
            $this->setValue($qb, 'perfect_view_nummer',     \PDO::PARAM_INT,  $pvRecord['Kaartnr']);

            // execute insert/update query
            $result = $this->conn->executeUpdate($qb->getSQL(), $qb->getParameters(), $qb->getParameterTypes());

            $this->logger->info('Query execution done', ['type' => $qb->getType(), 'result' => $result]);
        }
    }

    /**
     * @param string $perfectViewNummer
     * @return NULL|array Sollicitatie-record
     */
    protected function getSolliciatieRecord($perfectViewNummer)
    {
        $qb = $this->conn->createQueryBuilder()->select('e.*')->from('sollicitatie', 'e');
        $qb->where('e.perfect_view_nummer = :perfect_view_nummer')->setParameter('perfect_view_nummer', $perfectViewNummer);

        $stmt = $this->conn->executeQuery($qb->getSQL(), $qb->getParameters());

        if ($stmt->rowCount() === 0)
            return null;

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param number $perfectViewNummer
     * @return array Markt-record
     */
    protected function getMarktRecord($perfectViewNummer)
    {
        $this->preloadMarkten();

        if (isset($this->markten[$perfectViewNummer]) === false)
            return null;

        return $this->markten[$perfectViewNummer];
    }

    /**
     * Internal function to preload all the markt data, markt data is a small dataset which easy fits in memory
     */
    protected function preloadMarkten()
    {
        if ($this->markten !== null)
            return;

        $qb = $this->conn->createQueryBuilder()->select('e.*')->from('markt', 'e')->orderBy('e.perfect_view_nummer', 'ASC');
        $stmt = $this->conn->executeQuery($qb->getSQL());

        $this->markten = [];
        while ($record = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $this->markten[$record['perfect_view_nummer']] = $record;
        }
    }

    /**
     * @param string $erkenningsnummer
     * @return array Koopman-record
     */
    protected function getKoopmanRecord($erkenningsnummer)
    {
        // remove the dot from this value
        $erkenningsnummer = str_replace('.', '', $erkenningsnummer);

        $qb = $this->conn->createQueryBuilder()->select('e.*')->from('koopman', 'e');
        $qb->where('e.erkenningsnummer = :erkenningsnummer')->setParameter('erkenningsnummer', $erkenningsnummer);

        $stmt = $this->conn->executeQuery($qb->getSQL(), $qb->getParameters());

        if ($stmt->rowCount() === 0)
            return null;
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Helper method for marktstatus
     * @param string $status
     * @return string
     */
    private function convertMarktstatus($status)
    {
        if (in_array($status, ['vpl', 'vkk', 'soll']) === true)
            return $status;
        return '?';
    }

    /**
     * @param string $datetime_string
     * @return string
     */
    private function convertToDateTimeString($datetime_string)
    {
        $object = \DateTime::createFromFormat('d-m-Y H:i:s', $datetime_string);
        if ($object === false)
            return null;
        return $object->format('Y-m-d H:i:s');
    }

    /**
     * Helper method for array of vaste plaatsen from Perfect View record
     * @param array $pvRecord
     * @return multitype:string
     */
    private function getVastePlaatsenArray($pvRecord)
    {
        $a = [];
        if (isset($pvRecord['PLTSNR1']) === true && $pvRecord['PLTSNR1'] !== '')
            $a[] = $pvRecord['PLTSNR1'];
        if (isset($pvRecord['PLTSNR2']) === true && $pvRecord['PLTSNR2'] !== '')
            $a[] = $pvRecord['PLTSNR2'];
        if (isset($pvRecord['PLTSNR3']) === true && $pvRecord['PLTSNR3'] !== '')
            $a[] = $pvRecord['PLTSNR3'];
        if (isset($pvRecord['PLTSNR4']) === true && $pvRecord['PLTSNR4'] !== '')
            $a[] = $pvRecord['PLTSNR4'];
        if (isset($pvRecord['PLTSNR5']) === true && $pvRecord['PLTSNR5'] !== '')
            $a[] = $pvRecord['PLTSNR5'];
        if (isset($pvRecord['PLTSNR6']) === true && $pvRecord['PLTSNR6'] !== '')
            $a[] = $pvRecord['PLTSNR6'];
        return $a;
    }

    /**
     * Helper function to abstract INSERT and UPDATE
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $qb
     * @param string $field
     * @param string $value
     */
    private function setValue(\Doctrine\DBAL\Query\QueryBuilder $qb, $field, $type = null, $value = null)
    {
        if ($qb->getType() === QueryBuilder::UPDATE) {
            $qb->set($field, ':' . $field)->setParameter($field, $value, $type);
        } else {
            $qb->setValue($field, ':' . $field)->setParameter($field, $value, $type);
        }
    }

}