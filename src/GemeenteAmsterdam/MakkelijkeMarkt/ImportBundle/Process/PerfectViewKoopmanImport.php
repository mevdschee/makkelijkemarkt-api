<?php
/*
 *  Copyright (C) 2017 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Process;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\KoopmanRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Driver\PDOStatement;
use GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Utils\Logger;

class PerfectViewKoopmanImport
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
     * @var array
     */
    protected $soortStatusConversion = [
        'Actief' => Koopman::STATUS_ACTIEF,
        'Koopman' => Koopman::STATUS_ACTIEF,
        'Verwijderd' => Koopman::STATUS_VERWIJDERD,
        'Wachter' => Koopman::STATUS_WACHTER,
        'Vervanger' => Koopman::STATUS_VERVANGER,
    ];

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
        $requiredHeadings = ['Kaartnr', 'Erkenningsnummer', 'ACHTERNAAM', 'email', 'Telefoonnummer', 'Voorletters', 'Status', 'NFCHEX'];
        foreach ($requiredHeadings as $requiredHeading) {
            if (in_array($requiredHeading, $headings) === false) {
                throw new \RuntimeException('Missing column "' . $requiredHeading . '" in import file');
            }
        }

        $i = 0;
        $aantalNieuw = 0;
        $aantalBijgewerkt = 0;

        // iterate the csv-file
        foreach ($perfectViewRecords as $pvRecord) {
            // skip empty records
            if ($pvRecord === null || $pvRecord === '') {
                $this->logger->info('Skip, record is empty');
                continue;
            }

            $this->logger->info('Handle PerfectView record', ['Kaartnr' => $pvRecord['Kaartnr'], 'Erkenningsnummer' => $pvRecord['Erkenningsnummer']]);

            // get the record from the database (if it is already in the database)
            $koopmanRecord = $this->getKoopmanRecord($pvRecord['Kaartnr']);
            // prepare query builder
            $qb = $this->conn->createQueryBuilder();

            if (($koopmanRecord !== null)) {
                // update
                $this->logger->info('Record found in database', ['id' => $koopmanRecord['id']]);
                $qb->update('koopman', 'e');
                $qb->where('e.id = :id')->setParameter('id', $koopmanRecord['id']);
                $aantalNieuw ++;
            } else {
                // insert
                $this->logger->info('Record not in database, create new');
                $qb->insert('koopman');
                $qb->setValue('id', 'NEXTVAL(\'koopman_id_seq\')'); // IMPORTANT setValue on Query Builder, not via helper!
                $aantalBijgewerkt ++;
            }

            // set data
            $this->setValue($qb, 'erkenningsnummer',     \PDO::PARAM_STR,  str_replace('.', '', $pvRecord['Erkenningsnummer']));
            $this->setValue($qb, 'achternaam',           \PDO::PARAM_STR,  utf8_encode(str_replace('.', '', $pvRecord['ACHTERNAAM'])));
            $this->setValue($qb, 'email',                \PDO::PARAM_STR,  utf8_encode(str_replace('.', '', $pvRecord['email'])));
            $this->setValue($qb, 'telefoon',             \PDO::PARAM_STR,  str_replace('.', '', $pvRecord['Telefoonnummer']));
            $this->setValue($qb, 'voorletters',          \PDO::PARAM_STR,  utf8_encode(str_replace('.', '', $pvRecord['Voorletters'])));
            $this->setValue($qb, 'status',               \PDO::PARAM_STR,  $this->convertKoopmanStatus($pvRecord['Status']));
            $this->setValue($qb, 'perfect_view_nummer',  \PDO::PARAM_INT,  $pvRecord['Kaartnr']);
            $this->setValue($qb, 'pas_uid',              \PDO::PARAM_STR,  strtoupper($pvRecord['NFCHEX']));

            // execute insert/update query
            $result = $this->conn->executeUpdate($qb->getSQL(), $qb->getParameters(), $qb->getParameterTypes());

            $this->logger->info('Query execution done', ['type' => $qb->getType(), 'result' => $result]);
        }

        $this->logger->info('Alle records verwerkt', ['nieuw' => $aantalNieuw, 'bijgewerkt' => $aantalBijgewerkt, 'totaal' => $i]);

    }

    /**
     * @param string $perfectViewNummer
     * @return NULL|array Koopman-record
     */
    protected function getKoopmanRecord($perfectViewNummer)
    {
        $qb = $this->conn->createQueryBuilder()->select('e.*')->from('koopman', 'e');
        $qb->where('e.perfect_view_nummer = :perfect_view_nummer')->setParameter('perfect_view_nummer', $perfectViewNummer);

        $stmt = $this->conn->executeQuery($qb->getSQL(), $qb->getParameters());

        if ($stmt->rowCount() === 0)
            return null;

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Helper method for koopmanstatus
     * @param string $status
     * @return string
     */
    private function convertKoopmanStatus($status)
    {
        if (isset($this->soortStatusConversion[$status]) === true)
            return $this->soortStatusConversion[$status];
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