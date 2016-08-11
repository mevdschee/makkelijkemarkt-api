<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150826105615 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning ADD notitie TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD aanmaak_datumtijd TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('UPDATE dagvergunning SET aanmaak_datumtijd = registratie_datumtijd');
        $this->addSql('ALTER TABLE dagvergunning ALTER COLUMN aanmaak_datumtijd SET NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning DROP notitie');
        $this->addSql('ALTER TABLE dagvergunning DROP aanmaak_datumtijd');
    }
}
