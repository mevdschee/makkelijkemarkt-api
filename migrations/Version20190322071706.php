<?php

declare (strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190322071706 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE vergunning_controle ADD vervanger_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD sollicitatie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD erkenningsnummer_invoer_methode VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD erkenningsnummer_invoer_waarde VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD aantal3meter_kramen INT NOT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD aantal4meter_kramen INT NOT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD extra_meters INT NOT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD aantal_elektra INT NOT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD afvaleiland INT NOT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD eenmalig_elektra BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD afvaleiland_vast INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD krachtstroom BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD reiniging BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD aantal3meter_kramen_vast INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD aantal4meter_kramen_vast INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD aantal_extra_meters_vast INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD aantal_elektra_vast INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD krachtstroom_vast BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD status_solliciatie VARCHAR(4) DEFAULT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD notitie TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD CONSTRAINT FK_8C0115D53BF9138C FOREIGN KEY (vervanger_id) REFERENCES koopman (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vergunning_controle ADD CONSTRAINT FK_8C0115D5D6917333 FOREIGN KEY (sollicitatie_id) REFERENCES sollicitatie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8C0115D53BF9138C ON vergunning_controle (vervanger_id)');
        $this->addSql('CREATE INDEX IDX_8C0115D5D6917333 ON vergunning_controle (sollicitatie_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vergunning_controle DROP CONSTRAINT FK_8C0115D53BF9138C');
        $this->addSql('ALTER TABLE vergunning_controle DROP CONSTRAINT FK_8C0115D5D6917333');
        $this->addSql('DROP INDEX IDX_8C0115D53BF9138C');
        $this->addSql('DROP INDEX IDX_8C0115D5D6917333');
        $this->addSql('ALTER TABLE vergunning_controle DROP vervanger_id');
        $this->addSql('ALTER TABLE vergunning_controle DROP sollicitatie_id');
        $this->addSql('ALTER TABLE vergunning_controle DROP erkenningsnummer_invoer_methode');
        $this->addSql('ALTER TABLE vergunning_controle DROP erkenningsnummer_invoer_waarde');
        $this->addSql('ALTER TABLE vergunning_controle DROP aantal3meter_kramen');
        $this->addSql('ALTER TABLE vergunning_controle DROP aantal4meter_kramen');
        $this->addSql('ALTER TABLE vergunning_controle DROP extra_meters');
        $this->addSql('ALTER TABLE vergunning_controle DROP aantal_elektra');
        $this->addSql('ALTER TABLE vergunning_controle DROP afvaleiland');
        $this->addSql('ALTER TABLE vergunning_controle DROP eenmalig_elektra');
        $this->addSql('ALTER TABLE vergunning_controle DROP afvaleiland_vast');
        $this->addSql('ALTER TABLE vergunning_controle DROP krachtstroom');
        $this->addSql('ALTER TABLE vergunning_controle DROP reiniging');
        $this->addSql('ALTER TABLE vergunning_controle DROP aantal3meter_kramen_vast');
        $this->addSql('ALTER TABLE vergunning_controle DROP aantal4meter_kramen_vast');
        $this->addSql('ALTER TABLE vergunning_controle DROP aantal_extra_meters_vast');
        $this->addSql('ALTER TABLE vergunning_controle DROP aantal_elektra_vast');
        $this->addSql('ALTER TABLE vergunning_controle DROP krachtstroom_vast');
        $this->addSql('ALTER TABLE vergunning_controle DROP status_solliciatie');
        $this->addSql('ALTER TABLE vergunning_controle DROP notitie');
    }
}
