<?php

declare (strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150810150807 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning ADD registrant_invoer_waarde VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE dagvergunning DROP registrant_invoer');
        $this->addSql('ALTER TABLE dagvergunning RENAME COLUMN ingevoerd_via TO registrant_invoer_methode');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning ADD registrant_invoer VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD ingevoerd_via VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE dagvergunning DROP registrant_invoer_methode');
        $this->addSql('ALTER TABLE dagvergunning DROP registrant_invoer_waarde');
    }
}
