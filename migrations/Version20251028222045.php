<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251028222045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE moyenne (id SERIAL NOT NULL, eleve_id INT DEFAULT NULL, cycle_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F27AFF8FA6CC7B2 ON moyenne (eleve_id)');
        $this->addSql('CREATE INDEX IDX_F27AFF8F5EC1162 ON moyenne (cycle_id)');
        $this->addSql('ALTER TABLE moyenne ADD CONSTRAINT FK_F27AFF8FA6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE moyenne ADD CONSTRAINT FK_F27AFF8F5EC1162 FOREIGN KEY (cycle_id) REFERENCES cycle (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE moyenne DROP CONSTRAINT FK_F27AFF8FA6CC7B2');
        $this->addSql('ALTER TABLE moyenne DROP CONSTRAINT FK_F27AFF8F5EC1162');
        $this->addSql('DROP TABLE moyenne');
    }
}
