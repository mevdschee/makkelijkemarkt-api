<?php

declare (strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201126083929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account ALTER password TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE dagvergunning RENAME aantal3meter_kramen TO aantal3_meter_kramen');
        $this->addSql('ALTER TABLE dagvergunning RENAME aantal4meter_kramen TO aantal4_meter_kramen');
        $this->addSql('ALTER TABLE vergunning_controle RENAME aantal3meter_kramen TO aantal3_meter_kramen');
        $this->addSql('ALTER TABLE vergunning_controle RENAME aantal4meter_kramen TO aantal4_meter_kramen');
        $this->addSql('ALTER TABLE vergunning_controle ALTER status_solliciatie TYPE VARCHAR(15)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE account ALTER password TYPE VARCHAR(64)');
        $this->addSql('ALTER TABLE dagvergunning RENAME aantal3_meter_kramen TO aantal3meter_kramen');
        $this->addSql('ALTER TABLE dagvergunning RENAME aantal4_meter_kramen TO aantal4meter_kramen');
        $this->addSql('ALTER TABLE vergunning_controle RENAME aantal3_meter_kramen TO aantal3meter_kramen');
        $this->addSql('ALTER TABLE vergunning_controle RENAME aantal4_meter_kramen TO aantal4meter_kramen');
        $this->addSql('ALTER TABLE vergunning_controle ALTER status_solliciatie TYPE VARCHAR(4)');
    }
}
