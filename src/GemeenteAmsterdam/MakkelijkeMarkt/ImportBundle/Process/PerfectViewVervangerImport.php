<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Process;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\KoopmanRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Driver\PDOStatement;
use GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Utils\Logger;

class PerfectViewVervangerImport
{
    /**
     * @var KoopmanRepository
     */
    protected $koopmanRepository;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Logger
     */
    protected $logger;

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
     * @param array $perfectViewRecords
     */
    public function execute($perfectViewRecords)
    {
        $headings = $perfectViewRecords->getHeadings();
        $requiredHeadings = ['Vervanger_NFCHEX', 'Erkenningsnummer_Vervanger', 'Erkenningsnummer_Koopman'];
        foreach ($requiredHeadings as $requiredHeading) {
            if (in_array($requiredHeading, $headings) === false) {
                throw new \RuntimeException('Missing column "' . $requiredHeading . '" in import file');
            }
        }

        $sql = "DELETE FROM vervanger WHERE id >= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, 1);
        $stmt->execute();

        // iterate the csv-file
        foreach ($perfectViewRecords as $pvRecord) {
            // skip empty records
            if ($pvRecord === null || $pvRecord === '') {
                $this->logger->info('Skip, record is empty');
                continue;
            }

            $this->logger->info('Handle PerfectView record', ['Vervanger_NFCHEX' => $pvRecord['Vervanger_NFCHEX'], 'Erkenningsnummer_Vervanger' => $pvRecord['Erkenningsnummer_Vervanger'], 'Erkenningsnummer_Koopman' => $pvRecord['Erkenningsnummer_Koopman']]);

            // get the record from the database (if it is already in the database)
            $koopmanRecord = $this->getKoopmanRecord($pvRecord['Erkenningsnummer_Koopman']);
            $vervangerRecord = $this->getKoopmanRecord($pvRecord['Erkenningsnummer_Vervanger']);

            if (null === $koopmanRecord) {
                $this->logger->info('Can\'t find koopman, skipping.');
                continue;
            }
            if (null === $vervangerRecord) {
                $this->logger->info('Can\'t find vervanger, skipping.');
                continue;
            }

            // prepare query builder
            $qb = $this->conn->createQueryBuilder();

            $this->logger->info('Creating vervanger');
            $qb->insert('vervanger');
            $qb->setValue('id', 'NEXTVAL(\'vervanger_id_seq\')'); // IMPORTANT setValue on Query Builder, not via helper!


            // set data
            $this->setValue($qb, 'koopman_id',           \PDO::PARAM_INT,  $koopmanRecord['id']);
            $this->setValue($qb, 'vervanger_id',         \PDO::PARAM_INT,  $vervangerRecord['id']);
            $this->setValue($qb, 'pas_uid',              \PDO::PARAM_STR,  $pvRecord['Vervanger_NFCHEX']);

            // execute insert/update query
            $result = $this->conn->executeUpdate($qb->getSQL(), $qb->getParameters(), $qb->getParameterTypes());

            $this->logger->info('Query execution done', ['type' => $qb->getType(), 'result' => $result]);

        }

        $this->logger->info('Alle records verwerkt');

    }

    /**
     * @param string $erkenningsNummer
     * @return NULL|array Koopman-record
     */
    protected function getKoopmanRecord($erkenningsNummer)
    {
        $qb = $this->conn->createQueryBuilder()->select('e.*')->from('koopman', 'e');
        $qb->where('e.erkenningsnummer = :erkenningsnummer')->setParameter('erkenningsnummer', str_replace('.', '', $erkenningsNummer));

        $stmt = $this->conn->executeQuery($qb->getSQL(), $qb->getParameters());

        if ($stmt->rowCount() === 0)
            return null;

        return $stmt->fetch(\PDO::FETCH_ASSOC);
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