<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150921170925 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning ADD aantal3meter_kramen INT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD aantal4meter_kramen INT NULL');
        $this->addSql('UPDATE dagvergunning SET aantal3meter_kramen = 0, aantal4meter_kramen = 0');
        $this->addSql('ALTER TABLE dagvergunning ALTER COLUMN aantal3meter_kramen SET NOT NULL');
        $this->addSql('ALTER TABLE dagvergunning ALTER COLUMN aantal4meter_kramen SET NOT NULL');
        $this->addSql('DROP SEQUENCE plaats_id_seq CASCADE');
        $this->addSql('DROP TABLE plaats');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE plaats_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE plaats (id INT NOT NULL, dagvergunning_id INT NOT NULL, plaatsnummer VARCHAR(15) NOT NULL, meters INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_aef29b2bbe5f3a40 ON plaats (dagvergunning_id)');
        $this->addSql('ALTER TABLE plaats ADD CONSTRAINT fk_aef29b2bbe5f3a40 FOREIGN KEY (dagvergunning_id) REFERENCES dagvergunning (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dagvergunning DROP aantal3meter_kramen');
        $this->addSql('ALTER TABLE dagvergunning DROP aantal4meter_kramen');
    }
}
