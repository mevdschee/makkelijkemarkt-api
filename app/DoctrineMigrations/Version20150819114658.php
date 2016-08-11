<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150819114658 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning ADD doorgehaald BOOLEAN NULL');
        $this->addSql('UPDATE dagvergunning SET doorgehaald = FALSE');
        $this->addSql('ALTER TABLE dagvergunning ALTER COLUMN doorgehaald SET NOT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD doorgehaald_datumtijd TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD doorgehaald_geolocatie_lat DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD doorgehaald_geolocatie_long DOUBLE PRECISION DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning DROP doorgehaald');
        $this->addSql('ALTER TABLE dagvergunning DROP doorgehaald_datumtijd');
        $this->addSql('ALTER TABLE dagvergunning DROP doorgehaald_geolocatie_lat');
        $this->addSql('ALTER TABLE dagvergunning DROP doorgehaald_geolocatie_long');
    }
}
