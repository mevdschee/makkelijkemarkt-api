<?php

declare (strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190321112554 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE vergunning_controle ADD registratie_account INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vergunning_controle ADD CONSTRAINT FK_8C0115D5F612A8B0 FOREIGN KEY (registratie_account) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8C0115D5F612A8B0 ON vergunning_controle (registratie_account)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vergunning_controle DROP CONSTRAINT FK_8C0115D5F612A8B0');
        $this->addSql('DROP INDEX IDX_8C0115D5F612A8B0');
        $this->addSql('ALTER TABLE vergunning_controle DROP registratie_account');
    }
}
