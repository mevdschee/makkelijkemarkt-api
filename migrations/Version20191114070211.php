<?php

declare (strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20191114070211 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE markt ADD kies_je_kraam_mededeling_actief BOOLEAN NULL');
        $this->addSql('UPDATE markt SET kies_je_kraam_mededeling_actief = false');
        $this->addSql('ALTER TABLE markt ALTER COLUMN kies_je_kraam_mededeling_actief SET NOT NULL');

        $this->addSql('ALTER TABLE markt ADD kies_je_kraam_mededeling_titel TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE markt ADD kies_je_kraam_mededeling_tekst TEXT DEFAULT NULL');

        $this->addSql('ALTER TABLE markt ADD kies_je_kraam_actief BOOLEAN NULL');
        $this->addSql('UPDATE markt SET kies_je_kraam_actief = false');
        $this->addSql('ALTER TABLE markt ALTER COLUMN kies_je_kraam_actief SET NOT NULL');

        $this->addSql('ALTER TABLE markt ADD kies_je_kraam_fase VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE markt ADD markt_dagen_tekst TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE markt ADD indelings_tijdstip_tekst TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE markt ADD telefoon_nummer_contact TEXT DEFAULT NULL');

        $this->addSql('ALTER TABLE markt ADD makkelijke_markt_actief BOOLEAN NULL');
        $this->addSql('UPDATE markt SET makkelijke_markt_actief = true');
        $this->addSql('ALTER TABLE markt ALTER COLUMN makkelijke_markt_actief SET NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE markt DROP kies_je_kraam_mededeling_actief');
        $this->addSql('ALTER TABLE markt DROP kies_je_kraam_mededeling_titel');
        $this->addSql('ALTER TABLE markt DROP kies_je_kraam_mededeling_tekst');
        $this->addSql('ALTER TABLE markt DROP kies_je_kraam_actief');
        $this->addSql('ALTER TABLE markt DROP kies_je_kraam_fase');
        $this->addSql('ALTER TABLE markt DROP markt_dagen_tekst');
        $this->addSql('ALTER TABLE markt DROP indelings_tijdstip_tekst');
        $this->addSql('ALTER TABLE markt DROP telefoon_nummer_contact');
        $this->addSql('ALTER TABLE markt DROP makkelijke_markt_actief');
    }
}
