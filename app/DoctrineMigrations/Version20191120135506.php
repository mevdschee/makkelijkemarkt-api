<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20191120135506 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE lineairplan ADD elektra NUMERIC(10, 2) NULL');
        $this->addSql('UPDATE lineairplan SET elektra = 0');
        $this->addSql('ALTER TABLE lineairplan ALTER COLUMN elektra SET NOT NULL');
        $this->addSql('ALTER TABLE markt ADD kies_je_kraam_geblokeerde_plaatsen TEXT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE lineairplan DROP elektra');
        $this->addSql('ALTER TABLE markt DROP kies_je_kraam_geblokeerde_plaatsen');
    }
}
