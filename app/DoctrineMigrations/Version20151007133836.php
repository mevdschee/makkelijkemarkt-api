<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151007133836 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE account ADD username VARCHAR(255) NULL');
        $this->addSql('UPDATE account SET username = email');
        $this->addSql('ALTER TABLE account ALTER COLUMN username SET NOT NULL');

        $this->addSql('ALTER TABLE account ADD password VARCHAR(64) NULL');
        $this->addSql('UPDATE account SET password = \'\'');
        $this->addSql('ALTER TABLE account ALTER COLUMN password SET NOT NULL');

        $this->addSql('ALTER TABLE account ADD salt VARCHAR(64) NULL');
        $this->addSql('UPDATE account SET salt = \'\'');
        $this->addSql('ALTER TABLE account ALTER COLUMN salt SET NOT NULL');

        $this->addSql('ALTER TABLE account ADD is_active BOOLEAN NULL');
        $this->addSql('UPDATE account SET is_active = true');
        $this->addSql('ALTER TABLE account ALTER COLUMN is_active SET NOT NULL');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A4F85E0677 ON account (username)');
        $this->addSql('ALTER TABLE token DROP token_secret');
        $this->addSql('ALTER TABLE token DROP type');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX UNIQ_7D3656A4F85E0677');
        $this->addSql('ALTER TABLE account DROP username');
        $this->addSql('ALTER TABLE account DROP password');
        $this->addSql('ALTER TABLE account DROP salt');
        $this->addSql('ALTER TABLE account DROP is_active');
        $this->addSql('ALTER TABLE token ADD token_secret VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE token ADD type VARCHAR(7) NOT NULL');
    }
}
