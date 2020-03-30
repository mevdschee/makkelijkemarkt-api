<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190321102518 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE vergunning_controle_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE vergunning_controle (id INT NOT NULL, dagvergunning_id INT NOT NULL, aanwezig VARCHAR(50) NOT NULL, registratie_datumtijd TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, registratie_geolocatie_lat DOUBLE PRECISION DEFAULT NULL, registratie_geolocatie_long DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8C0115D5BE5F3A40 ON vergunning_controle (dagvergunning_id)');
        $this->addSql('ALTER TABLE vergunning_controle ADD CONSTRAINT FK_8C0115D5BE5F3A40 FOREIGN KEY (dagvergunning_id) REFERENCES dagvergunning (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE vergunning_controle_id_seq CASCADE');
        $this->addSql('DROP TABLE vergunning_controle');
    }
}
