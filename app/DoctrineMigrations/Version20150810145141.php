<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150810145141 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE dagvergunning_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE dagvergunning (id INT NOT NULL, markt_id INT NOT NULL, registrant_id INT DEFAULT NULL, dag DATE NOT NULL, registrant_invoer VARCHAR(255) DEFAULT NULL, ingevoerd_via VARCHAR(50) NOT NULL, registratie_datumtijd TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, registratie_geolocatie_lat DOUBLE PRECISION DEFAULT NULL, registratie_geolocatie_long DOUBLE PRECISION DEFAULT NULL, plaatsnummer VARCHAR(25) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F5B1737D658EC2D ON dagvergunning (markt_id)');
        $this->addSql('CREATE INDEX IDX_8F5B17373304A716 ON dagvergunning (registrant_id)');
        $this->addSql('ALTER TABLE dagvergunning ADD CONSTRAINT FK_8F5B1737D658EC2D FOREIGN KEY (markt_id) REFERENCES markt (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dagvergunning ADD CONSTRAINT FK_8F5B17373304A716 FOREIGN KEY (registrant_id) REFERENCES registrant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE dagvergunning_id_seq CASCADE');
        $this->addSql('DROP TABLE dagvergunning');
    }
}
