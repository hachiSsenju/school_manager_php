<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251021131633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grade_h ADD bulletin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE grade_h ADD CONSTRAINT FK_B5D79E4CD1AAB236 FOREIGN KEY (bulletin_id) REFERENCES bulletin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B5D79E4CD1AAB236 ON grade_h (bulletin_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE grade_h DROP CONSTRAINT FK_B5D79E4CD1AAB236');
        $this->addSql('DROP INDEX IDX_B5D79E4CD1AAB236');
        $this->addSql('ALTER TABLE grade_h DROP bulletin_id');
    }
}
