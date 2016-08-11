<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160317105514 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE lineairplan ADD eenmalig_elektra NUMERIC(10, 2)');
        $this->addSql("UPDATE lineairplan SET eenmalig_elektra = 0");
        $this->addSql('ALTER TABLE lineairplan ALTER eenmalig_elektra SET NOT NULL');
        $this->addSql('ALTER TABLE concreetplan ADD eenmalig_elektra NUMERIC(10, 2)');
        $this->addSql("UPDATE concreetplan SET eenmalig_elektra = 0");
        $this->addSql('ALTER TABLE concreetplan ALTER eenmalig_elektra SET NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE lineairplan DROP eenmalig_elektra');
        $this->addSql('ALTER TABLE concreetplan DROP eenmalig_elektra');
    }
}