<?php

declare (strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151007150245 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning ADD registratie_account INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD doorgehaald_account INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dagvergunning ADD CONSTRAINT FK_8F5B1737F612A8B0 FOREIGN KEY (registratie_account) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dagvergunning ADD CONSTRAINT FK_8F5B1737A5ED57DF FOREIGN KEY (doorgehaald_account) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8F5B1737F612A8B0 ON dagvergunning (registratie_account)');
        $this->addSql('CREATE INDEX IDX_8F5B1737A5ED57DF ON dagvergunning (doorgehaald_account)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning DROP CONSTRAINT FK_8F5B1737F612A8B0');
        $this->addSql('ALTER TABLE dagvergunning DROP CONSTRAINT FK_8F5B1737A5ED57DF');
        $this->addSql('DROP INDEX IDX_8F5B1737F612A8B0');
        $this->addSql('DROP INDEX IDX_8F5B1737A5ED57DF');
        $this->addSql('ALTER TABLE dagvergunning DROP registratie_account');
        $this->addSql('ALTER TABLE dagvergunning DROP doorgehaald_account');
    }
}
