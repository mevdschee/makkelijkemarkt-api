<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150909100008 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning ADD sollicitatie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD aantal3meter_kramen_vast INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD aantal4meter_kramen_vast INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD aantal_extra_meters_vast INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD aantal_elektra_vast INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD krachtstroom_vast BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD status_solliciatie VARCHAR(4) DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD CONSTRAINT FK_8F5B1737D6917333 FOREIGN KEY (sollicitatie_id) REFERENCES sollicitatie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8F5B1737D6917333 ON dagvergunning (sollicitatie_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning DROP CONSTRAINT FK_8F5B1737D6917333');
        $this->addSql('DROP INDEX IDX_8F5B1737D6917333');
        $this->addSql('ALTER TABLE dagvergunning DROP sollicitatie_id');
        $this->addSql('ALTER TABLE dagvergunning DROP aantal3meter_kramen_vast');
        $this->addSql('ALTER TABLE dagvergunning DROP aantal4meter_kramen_vast');
        $this->addSql('ALTER TABLE dagvergunning DROP aantal_extra_meters_vast');
        $this->addSql('ALTER TABLE dagvergunning DROP aantal_elektra_vast');
        $this->addSql('ALTER TABLE dagvergunning DROP krachtstroom_vast');
        $this->addSql('ALTER TABLE dagvergunning DROP status_solliciatie');
    }
}
