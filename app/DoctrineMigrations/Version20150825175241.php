<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150825175241 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE markt ADD standaard_kraam_afmeting INT DEFAULT NULL');
        $this->addSql('ALTER TABLE markt ADD extra_meters_mogelijk BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE markt ADD aanwezige_opties TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN markt.aanwezige_opties IS \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE markt_extra_data ADD aanwezige_opties TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN markt_extra_data.aanwezige_opties IS \'(DC2Type:json_array)\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE markt_extra_data DROP aanwezige_opties');
        $this->addSql('ALTER TABLE markt DROP standaard_kraam_afmeting');
        $this->addSql('ALTER TABLE markt DROP extra_meters_mogelijk');
        $this->addSql('ALTER TABLE markt DROP aanwezige_opties');
    }
}
