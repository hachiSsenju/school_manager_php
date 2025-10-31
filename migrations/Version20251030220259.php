<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251030220259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grade_h DROP CONSTRAINT fk_b5d79e4ca6d57929');
        $this->addSql('DROP INDEX idx_b5d79e4ca6d57929');
        $this->addSql('ALTER TABLE grade_h DROP trimester_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE grade_h ADD trimester_id INT NOT NULL');
        $this->addSql('ALTER TABLE grade_h ADD CONSTRAINT fk_b5d79e4ca6d57929 FOREIGN KEY (trimester_id) REFERENCES trimester (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b5d79e4ca6d57929 ON grade_h (trimester_id)');
    }
}
