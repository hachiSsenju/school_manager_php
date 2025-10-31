<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251031205602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grade_p DROP CONSTRAINT fk_a6bb061aa6d57929');
        $this->addSql('ALTER TABLE cycle DROP CONSTRAINT fk_b086d193a6d57929');
        $this->addSql('DROP SEQUENCE grade_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE trimester_id_seq CASCADE');
        $this->addSql('ALTER TABLE grade DROP CONSTRAINT fk_595aae34a6cc7b2');
        $this->addSql('ALTER TABLE grade DROP CONSTRAINT fk_595aae34a6d57929');
        $this->addSql('ALTER TABLE grade DROP CONSTRAINT fk_595aae34d1aab236');
        $this->addSql('ALTER TABLE grade DROP CONSTRAINT fk_595aae34f46cd258');
        $this->addSql('ALTER TABLE trimester DROP CONSTRAINT fk_d256bd198f5ea509');
        $this->addSql('DROP TABLE grade');
        $this->addSql('DROP TABLE trimester');
        $this->addSql('DROP INDEX idx_b086d193a6d57929');
        $this->addSql('ALTER TABLE cycle DROP trimester_id');
        $this->addSql('DROP INDEX idx_a6bb061aa6d57929');
        $this->addSql('ALTER TABLE grade_p DROP trimester_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE grade_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE trimester_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE grade (id SERIAL NOT NULL, eleve_id INT NOT NULL, matiere_id INT NOT NULL, bulletin_id INT DEFAULT NULL, trimester_id INT NOT NULL, note INT NOT NULL, note_maximal INT NOT NULL, type_examen VARCHAR(255) NOT NULL, date VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_595aae34a6cc7b2 ON grade (eleve_id)');
        $this->addSql('CREATE INDEX idx_595aae34a6d57929 ON grade (trimester_id)');
        $this->addSql('CREATE INDEX idx_595aae34d1aab236 ON grade (bulletin_id)');
        $this->addSql('CREATE INDEX idx_595aae34f46cd258 ON grade (matiere_id)');
        $this->addSql('CREATE TABLE trimester (id SERIAL NOT NULL, classe_id INT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_d256bd198f5ea509 ON trimester (classe_id)');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT fk_595aae34a6cc7b2 FOREIGN KEY (eleve_id) REFERENCES eleve (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT fk_595aae34a6d57929 FOREIGN KEY (trimester_id) REFERENCES trimester (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT fk_595aae34d1aab236 FOREIGN KEY (bulletin_id) REFERENCES bulletin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT fk_595aae34f46cd258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trimester ADD CONSTRAINT fk_d256bd198f5ea509 FOREIGN KEY (classe_id) REFERENCES classe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade_p ADD trimester_id INT NOT NULL');
        $this->addSql('ALTER TABLE grade_p ADD CONSTRAINT fk_a6bb061aa6d57929 FOREIGN KEY (trimester_id) REFERENCES trimester (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_a6bb061aa6d57929 ON grade_p (trimester_id)');
        $this->addSql('ALTER TABLE cycle ADD trimester_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cycle ADD CONSTRAINT fk_b086d193a6d57929 FOREIGN KEY (trimester_id) REFERENCES trimester (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b086d193a6d57929 ON cycle (trimester_id)');
    }
}
