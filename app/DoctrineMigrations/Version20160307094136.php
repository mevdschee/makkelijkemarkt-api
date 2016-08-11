<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160307094136 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE lineairplan ADD afvaleiland NUMERIC(10, 2)');
        $this->addSql("UPDATE lineairplan SET afvaleiland = 0");
        $this->addSql('ALTER TABLE lineairplan ALTER afvaleiland SET NOT NULL');
        $this->addSql('ALTER TABLE concreetplan ADD afvaleiland NUMERIC(10, 2)');
        $this->addSql("UPDATE concreetplan SET afvaleiland = 0");
        $this->addSql('ALTER TABLE concreetplan ALTER afvaleiland SET NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE lineairplan DROP afvaleiland');
        $this->addSql('ALTER TABLE concreetplan DROP afvaleiland');
    }
}