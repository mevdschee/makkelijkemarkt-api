<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150819101744 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE plaats_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE plaats (id INT NOT NULL, dagvergunning_id INT NOT NULL, plaatsnummer VARCHAR(15) NOT NULL, meters INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AEF29B2BBE5F3A40 ON plaats (dagvergunning_id)');
        $this->addSql('ALTER TABLE plaats ADD CONSTRAINT FK_AEF29B2BBE5F3A40 FOREIGN KEY (dagvergunning_id) REFERENCES dagvergunning (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dagvergunning DROP plaatsnummer');
        $this->addSql('ALTER TABLE dagvergunning DROP extra_plaatsnummers');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE plaats_id_seq CASCADE');
        $this->addSql('DROP TABLE plaats');
        $this->addSql('ALTER TABLE dagvergunning ADD plaatsnummer VARCHAR(25) NOT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD extra_plaatsnummers TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN dagvergunning.extra_plaatsnummers IS \'(DC2Type:simple_array)\'');
    }
}
