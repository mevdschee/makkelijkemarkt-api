<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150907091518 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE token (uuid VARCHAR(36) NOT NULL, account_id INT DEFAULT NULL, token_secret VARCHAR(36) NOT NULL, creation_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, life_time INT NOT NULL, type VARCHAR(7) NOT NULL, device_uuid VARCHAR(255) DEFAULT NULL, client_app VARCHAR(255) DEFAULT NULL, client_version VARCHAR(25) DEFAULT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_5F37A13B9B6B5FBA ON token (account_id)');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13B9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE token');
    }
}
