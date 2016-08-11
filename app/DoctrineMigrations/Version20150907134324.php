<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150907134324 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning ADD aantal_elektra INT DEFAULT NULL');
        $this->addSql('UPDATE dagvergunning SET aantal_elektra = 0');
        $this->addSql('ALTER TABLE dagvergunning ALTER COLUMN aantal_elektra SET NOT NULL');

        $this->addSql('ALTER TABLE dagvergunning ADD krachtstroom BOOLEAN DEFAULT NULL');
        $this->addSql('UPDATE dagvergunning SET krachtstroom = false');
        $this->addSql('ALTER TABLE dagvergunning ALTER COLUMN krachtstroom SET NOT NULL');

        $this->addSql('ALTER TABLE dagvergunning ADD reiniging BOOLEAN DEFAULT NULL');
        $this->addSql('UPDATE dagvergunning SET reiniging = false');
        $this->addSql('ALTER TABLE dagvergunning ALTER COLUMN reiniging SET NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning DROP aantal_elektra');
        $this->addSql('ALTER TABLE dagvergunning DROP krachtstroom');
        $this->addSql('ALTER TABLE dagvergunning DROP reiniging');
    }
}
