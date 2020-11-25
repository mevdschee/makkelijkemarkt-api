<?php

declare (strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181216192154 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE markt_extra_data DROP CONSTRAINT markt_extra_data_pkey');
        $this->addSql('ALTER TABLE markt_extra_data ADD afkorting VARCHAR(255) NULL');
        $this->addSql('ALTER TABLE markt_extra_data ALTER perfect_view_nummer DROP NOT NULL');
        $this->addSql('UPDATE markt_extra_data SET afkorting = (SELECT afkorting FROM markt WHERE markt.perfect_view_nummer = markt_extra_data.perfect_view_nummer)');
        $this->addSql('ALTER TABLE markt_extra_data ALTER COLUMN afkorting SET NOT NULL');
        $this->addSql('ALTER TABLE markt_extra_data ADD PRIMARY KEY (afkorting)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX markt_extra_data_pkey');
        $this->addSql('ALTER TABLE markt_extra_data DROP afkorting');
        $this->addSql('ALTER TABLE markt_extra_data ALTER perfect_view_nummer SET NOT NULL');
        $this->addSql('ALTER TABLE markt_extra_data ADD PRIMARY KEY (perfect_view_nummer)');
    }
}
