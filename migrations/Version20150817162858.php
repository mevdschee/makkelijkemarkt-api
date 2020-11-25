<?php

declare (strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150817162858 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning DROP CONSTRAINT fk_8f5b17373304a716');
        $this->addSql('DROP INDEX idx_8f5b17373304a716');
        $this->addSql('ALTER TABLE dagvergunning RENAME COLUMN registrant_id TO koopman_id');
        $this->addSql('ALTER TABLE dagvergunning ADD CONSTRAINT FK_8F5B1737FE3565D3 FOREIGN KEY (koopman_id) REFERENCES koopman (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8F5B1737FE3565D3 ON dagvergunning (koopman_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dagvergunning DROP CONSTRAINT FK_8F5B1737FE3565D3');
        $this->addSql('DROP INDEX IDX_8F5B1737FE3565D3');
        $this->addSql('ALTER TABLE dagvergunning RENAME COLUMN koopman_id TO registrant_id');
        $this->addSql('ALTER TABLE dagvergunning ADD CONSTRAINT fk_8f5b17373304a716 FOREIGN KEY (registrant_id) REFERENCES koopman (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8f5b17373304a716 ON dagvergunning (registrant_id)');
    }
}
