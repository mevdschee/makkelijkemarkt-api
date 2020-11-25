<?php

declare (strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150817165612 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning RENAME COLUMN registrant_invoer_methode TO erkenningsnummer_invoer_methode;');
        $this->addSql('ALTER TABLE dagvergunning RENAME COLUMN registrant_invoer_waarde TO erkenningsnummer_invoer_waarde;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning RENAME COLUMN erkenningsnummer_invoer_methode TO registrant_invoer_methode;');
        $this->addSql('ALTER TABLE dagvergunning RENAME COLUMN erkenningsnummer_invoer_waarde TO registrant_invoer_waarde;');
    }
}
