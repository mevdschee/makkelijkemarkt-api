<?php

declare (strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150831085918 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE sollicitatie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE sollicitatie (id INT NOT NULL, markt_id INT NOT NULL, koopman_id INT NOT NULL, sollicitatie_nummer INT NOT NULL, status VARCHAR(4) NOT NULL, inschrijf_datum TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, doorgehaald BOOLEAN NOT NULL, doorgehaald_reden VARCHAR(255) DEFAULT NULL, perfect_view_nummer INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9577817DD658EC2D ON sollicitatie (markt_id)');
        $this->addSql('CREATE INDEX IDX_9577817DFE3565D3 ON sollicitatie (koopman_id)');
        $this->addSql('CREATE INDEX sollicitatieSollicitatieNummer ON sollicitatie (sollicitatie_nummer)');
        $this->addSql('CREATE INDEX sollicitatieMarktSollicitatieNummer ON sollicitatie (markt_id, sollicitatie_nummer)');
        $this->addSql('CREATE INDEX sollicitatiePerfectViewNumber ON sollicitatie (perfect_view_nummer)');
        $this->addSql('ALTER TABLE sollicitatie ADD CONSTRAINT FK_9577817DD658EC2D FOREIGN KEY (markt_id) REFERENCES markt (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sollicitatie ADD CONSTRAINT FK_9577817DFE3565D3 FOREIGN KEY (koopman_id) REFERENCES koopman (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE sollicitatie_id_seq CASCADE');
        $this->addSql('DROP TABLE sollicitatie');
    }
}
