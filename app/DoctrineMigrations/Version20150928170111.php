<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150928170111 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE notitie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE notitie (id INT NOT NULL, markt_id INT NOT NULL, dag DATE NOT NULL, bericht TEXT DEFAULT NULL, afgevinkt_status BOOLEAN NOT NULL, verwijderd BOOLEAN NOT NULL, aangemaakt_datumtijd TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, aangemaakt_geolocatie_lat DOUBLE PRECISION DEFAULT NULL, aangemaakt_geolocatie_long DOUBLE PRECISION DEFAULT NULL, afgevinkt_datumtijd TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, verwijderd_datumtijd TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2F242E7AD658EC2D ON notitie (markt_id)');
        $this->addSql('ALTER TABLE notitie ADD CONSTRAINT FK_2F242E7AD658EC2D FOREIGN KEY (markt_id) REFERENCES markt (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE notitie_id_seq CASCADE');
        $this->addSql('DROP TABLE notitie');
    }
}
