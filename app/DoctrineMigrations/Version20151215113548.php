<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151215113548 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE lineairplan_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tariefplan_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE concreetplan_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE factuur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE product (id INT NOT NULL, factuur_id INT DEFAULT NULL, naam VARCHAR(255) NOT NULL, bedrag NUMERIC(10, 2) NOT NULL, aantal INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D34A04ADC35D3E ON product (factuur_id)');
        $this->addSql('CREATE TABLE lineairplan (id INT NOT NULL, tariefplan_id INT DEFAULT NULL, tarief_per_meter NUMERIC(10, 2) NOT NULL, reiniging_per_meter NUMERIC(10, 2) NOT NULL, toeslag_bedrijfsafval_per_meter NUMERIC(10, 2) NOT NULL, toeslag_krachtstroom_per_aansluiting NUMERIC(10, 2) NOT NULL, promotie_gelden_per_meter NUMERIC(10, 2) NOT NULL, promotie_gelden_per_kraam NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D27404D38F7044D ON lineairplan (tariefplan_id)');
        $this->addSql('CREATE TABLE tariefplan (id INT NOT NULL, lineairplan_id INT DEFAULT NULL, concreetplan_id INT DEFAULT NULL, markt_id INT NOT NULL, naam VARCHAR(255) NOT NULL, geldig_vanaf TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, geldig_tot TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_153EDFCD4B7021A0 ON tariefplan (lineairplan_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_153EDFCD2F9BDDD6 ON tariefplan (concreetplan_id)');
        $this->addSql('CREATE INDEX IDX_153EDFCDD658EC2D ON tariefplan (markt_id)');
        $this->addSql('CREATE TABLE concreetplan (id INT NOT NULL, tariefplan_id INT DEFAULT NULL, een_meter NUMERIC(10, 2) NOT NULL, drie_meter NUMERIC(10, 2) NOT NULL, vier_meter NUMERIC(10, 2) NOT NULL, elektra NUMERIC(10, 2) NOT NULL, promotie_gelden_per_meter NUMERIC(10, 2) NOT NULL, promotie_gelden_per_kraam NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D27B3AE338F7044D ON concreetplan (tariefplan_id)');
        $this->addSql('CREATE TABLE factuur (id INT NOT NULL, dagvergunning_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32147710BE5F3A40 ON factuur (dagvergunning_id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADC35D3E FOREIGN KEY (factuur_id) REFERENCES factuur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lineairplan ADD CONSTRAINT FK_6D27404D38F7044D FOREIGN KEY (tariefplan_id) REFERENCES tariefplan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tariefplan ADD CONSTRAINT FK_153EDFCD4B7021A0 FOREIGN KEY (lineairplan_id) REFERENCES lineairplan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tariefplan ADD CONSTRAINT FK_153EDFCD2F9BDDD6 FOREIGN KEY (concreetplan_id) REFERENCES concreetplan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tariefplan ADD CONSTRAINT FK_153EDFCDD658EC2D FOREIGN KEY (markt_id) REFERENCES markt (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE concreetplan ADD CONSTRAINT FK_D27B3AE338F7044D FOREIGN KEY (tariefplan_id) REFERENCES tariefplan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE factuur ADD CONSTRAINT FK_32147710BE5F3A40 FOREIGN KEY (dagvergunning_id) REFERENCES dagvergunning (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dagvergunning ADD factuur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD CONSTRAINT FK_8F5B1737C35D3E FOREIGN KEY (factuur_id) REFERENCES factuur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8F5B1737C35D3E ON dagvergunning (factuur_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tariefplan DROP CONSTRAINT FK_153EDFCD4B7021A0');
        $this->addSql('ALTER TABLE lineairplan DROP CONSTRAINT FK_6D27404D38F7044D');
        $this->addSql('ALTER TABLE concreetplan DROP CONSTRAINT FK_D27B3AE338F7044D');
        $this->addSql('ALTER TABLE tariefplan DROP CONSTRAINT FK_153EDFCD2F9BDDD6');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04ADC35D3E');
        $this->addSql('ALTER TABLE dagvergunning DROP CONSTRAINT FK_8F5B1737C35D3E');
        $this->addSql('DROP SEQUENCE product_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE lineairplan_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tariefplan_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE concreetplan_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE factuur_id_seq CASCADE');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE lineairplan');
        $this->addSql('DROP TABLE tariefplan');
        $this->addSql('DROP TABLE concreetplan');
        $this->addSql('DROP TABLE factuur');
        $this->addSql('DROP INDEX UNIQ_8F5B1737C35D3E');
        $this->addSql('ALTER TABLE dagvergunning DROP factuur_id');
    }
}