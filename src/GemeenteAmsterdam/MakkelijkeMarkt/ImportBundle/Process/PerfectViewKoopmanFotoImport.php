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
class PerfectViewKoopmanFotoImport
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $conn;

    /**
     * @var string
     */
    protected $dataDir;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param \Doctrine\DBAL\Connection $conn
     * @param string $dataDir
     */
    public function __construct(\Doctrine\DBAL\Connection $conn, $dataDir)
    {
        $this->conn = $conn;
        $this->dataDir = $dataDir;
    }

    /**
     * Data directory initialization
     * @return boolean
     */
    protected function prepareDataDir()
    {
        if (file_exists($this->dataDir) === false) {
            $this->logger->info('dataDirectory does not exists, will be created', ['dataDirectory' => $this->dataDir]);
            $result = mkdir($this->dataDir);
            if ($result === false) {
                $this->logger->error('Can not create dataDirectory');
                return false;
            }
        }
        if (file_exists($this->dataDir) === true && is_dir($this->dataDir) === false) {
            $this->logger->error('dataDirectory is a file');
            return false;
        }
        if (file_exists($this->dataDir . DIRECTORY_SEPARATOR . 'koopman-fotos') === false) {
            $this->logger->info('dataDirectory/koopman-fotos does not exists, will be created', ['dataDirectory' => $this->dataDir]);
            $result = mkdir($this->dataDir . DIRECTORY_SEPARATOR . 'koopman-fotos');
            if ($result === false) {
                $this->logger->error('Can not create dataDirectory/koopman-fotos');
                return false;
            }
        }
        if (file_exists($this->dataDir . DIRECTORY_SEPARATOR . 'koopman-fotos') === true && is_dir($this->dataDir . DIRECTORY_SEPARATOR . 'koopman-fotos') === false) {
            $this->logger->error('dataDirectory/koopman-fotos is a file');
            return false;
        }
        return true;
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
     * @param string $imageSourceDirectory
     */
    public function execute(CsvIterator $content, $imageSourceDirectory)
    {
        $headings = $content->getHeadings();
        $requiredHeadings = ['Erkenningsnummer', 'FotoKop'];
        foreach ($requiredHeadings as $requiredHeading) {
            if (in_array($requiredHeading, $headings) === false) {
                throw new \RuntimeException('Missing column "' . $requiredHeading . '" in import file');
            }
        }

        $result = $this->prepareDataDir();
        if ($result === false) {
            $this->logger->emergency('dataDirectory is not ready, abort');
            return;
        }

        // iterate the csv-file
        foreach ($content as $pvRecord) {

            // skip empty records
            if ($pvRecord === null || $pvRecord === '') {
                $this->logger->info('Skip, record is empty');
                continue;
            }
            $this->logger->info('Handle PerfectView record', ['Erkenningsnummer' => $pvRecord['Erkenningsnummer'], 'FotoKop' => $pvRecord['FotoKop']]);

            // get relation fields
            $koopman = $this->getKoopmanRecord($pvRecord['Erkenningsnummer']);
            if ($koopman === null) {
                $this->logger->warning('Skip record, KOOPMAN not found in database', ['Erkenningsnummer' => $pvRecord['Erkenningsnummer']]);
                continue;
            }

            // skip empty foto record
            if ($pvRecord['FotoKop'] === '') {
                $this->logger->warning('Skip record, FOTO field is empty', ['Erkenningsnummer' => $pvRecord['Erkenningsnummer']]);
                continue;
            }

            // rewrite foto kop value
            $pvRecord['FotoKop'] = str_replace(['\\\\basis.lan\\amsterdamapps\\SDC\\PerfectviewMarktbureau\\Fotos\\'], '', $pvRecord['FotoKop']);

            // get expected import path
            $fullPath = $imageSourceDirectory . DIRECTORY_SEPARATOR . $pvRecord['FotoKop'];
            if (file_exists($fullPath) === false) {
                $this->logger->warning('Skip record, FILE does not exists', ['Erkenningsnummer' => $pvRecord['Erkenningsnummer'], 'FotoKop' => $pvRecord['FotoKop'], 'fullPath' => $fullPath]);
                continue;
            }

            // calculate checksum
            $checksum = md5_file($fullPath, false);
            if ($koopman['foto_hash'] === $checksum) {
                $this->logger->info('Skip, old hash and new hash are the same, photo not updated', ['Erkenningsnummer' => $pvRecord['Erkenningsnummer'], 'FotoKop' => $pvRecord['FotoKop'], 'NEW HASH' => $checksum, 'OLD HASH' => $koopman['foto_hash']]);
                continue;
            }

            // prepare query builder
            $qb = $this->conn->createQueryBuilder();
            $qb->update('koopman', 'e');
            $qb->where('e.id = :id')->setParameter('id', $koopman['id']);

            // copy the file to the new location
            $filename = time() . '-' . $checksum . '-' . $koopman['erkenningsnummer'] . '.jpg';
            $destination = $this->dataDir . DIRECTORY_SEPARATOR . 'koopman-fotos' . DIRECTORY_SEPARATOR . $filename;
            $result = copy($fullPath, $destination);
            if ($result === false) {
                $this->logger->error('Can not copy photo to data directory', ['Erkenningsnummer' => $pvRecord['Erkenningsnummer'], 'src' => $fullPath, 'dst' => $destination]);
                continue;
            }

            // set data
            $this->setValue($qb, 'foto',                \PDO::PARAM_STR,  $filename);
            $this->setValue($qb, 'foto_last_update',    \PDO::PARAM_STR,  date('Y-m-d H:i:s'));
            $this->setValue($qb, 'foto_hash',           \PDO::PARAM_STR,  $checksum);

            // execute insert/update query
            $result = $this->conn->executeUpdate($qb->getSQL(), $qb->getParameters(), $qb->getParameterTypes());

            $this->logger->info('Query execution done', ['type' => $qb->getType(), 'result' => $result]);
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