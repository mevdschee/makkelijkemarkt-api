<?php

declare (strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160506100408 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE account ADD attempts INT');
        $this->addSql('UPDATE account SET attempts = 0');
        $this->addSql('ALTER TABLE account ALTER attempts SET NOT NULL');
        $this->addSql('ALTER TABLE account ADD last_attempt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD locked BOOLEAN');
        $this->addSql('UPDATE account SET locked = false');
        $this->addSql('ALTER TABLE account ALTER locked SET NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE account DROP attempts');
        $this->addSql('ALTER TABLE account DROP last_attempt');
        $this->addSql('ALTER TABLE account DROP locked');
    }
}
