<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251030212502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bulletin (id SERIAL NOT NULL, eleve_id INT NOT NULL, classe_id INT NOT NULL, ecole_id INT NOT NULL, redoublant BOOLEAN NOT NULL, annee_scholaire VARCHAR(255) NOT NULL, mention VARCHAR(255) NOT NULL, rang VARCHAR(255) NOT NULL, moy_annuelle VARCHAR(255) NOT NULL, heure_absence VARCHAR(255) NOT NULL, date VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2B7D8942A6CC7B2 ON bulletin (eleve_id)');
        $this->addSql('CREATE INDEX IDX_2B7D89428F5EA509 ON bulletin (classe_id)');
        $this->addSql('CREATE INDEX IDX_2B7D894277EF1B1E ON bulletin (ecole_id)');
        $this->addSql('CREATE TABLE classe (id SERIAL NOT NULL, ecole_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, niveau VARCHAR(255) NOT NULL, frais INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F87BF9677EF1B1E ON classe (ecole_id)');
        $this->addSql('CREATE TABLE cycle (id SERIAL NOT NULL, bulletin_id INT NOT NULL, trimester_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B086D193D1AAB236 ON cycle (bulletin_id)');
        $this->addSql('CREATE INDEX IDX_B086D193A6D57929 ON cycle (trimester_id)');
        $this->addSql('CREATE TABLE ecole (id SERIAL NOT NULL, utilisateur_id INT NOT NULL, nom VARCHAR(255) NOT NULL, logo BYTEA DEFAULT NULL, phone VARCHAR(255) NOT NULL, directeur VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9786AACFB88E14F ON ecole (utilisateur_id)');
        $this->addSql('CREATE TABLE eleve (id SERIAL NOT NULL, classe_id INT DEFAULT NULL, ecole_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, birthday VARCHAR(255) NOT NULL, solde_initial INT NOT NULL, email_parent VARCHAR(255) DEFAULT NULL, matricule VARCHAR(255) NOT NULL, sexe VARCHAR(255) NOT NULL, birthplace VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ECA105F78F5EA509 ON eleve (classe_id)');
        $this->addSql('CREATE INDEX IDX_ECA105F777EF1B1E ON eleve (ecole_id)');
        $this->addSql('CREATE TABLE grade (id SERIAL NOT NULL, eleve_id INT NOT NULL, matiere_id INT NOT NULL, bulletin_id INT DEFAULT NULL, trimester_id INT NOT NULL, note INT NOT NULL, note_maximal INT NOT NULL, type_examen VARCHAR(255) NOT NULL, date VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_595AAE34A6CC7B2 ON grade (eleve_id)');
        $this->addSql('CREATE INDEX IDX_595AAE34F46CD258 ON grade (matiere_id)');
        $this->addSql('CREATE INDEX IDX_595AAE34D1AAB236 ON grade (bulletin_id)');
        $this->addSql('CREATE INDEX IDX_595AAE34A6D57929 ON grade (trimester_id)');
        $this->addSql('CREATE TABLE grade_h (id SERIAL NOT NULL, matiere_id INT DEFAULT NULL, trimester_id INT NOT NULL, cycle_id INT NOT NULL, bulletin_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, date VARCHAR(255) NOT NULL, note DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B5D79E4CF46CD258 ON grade_h (matiere_id)');
        $this->addSql('CREATE INDEX IDX_B5D79E4CA6D57929 ON grade_h (trimester_id)');
        $this->addSql('CREATE INDEX IDX_B5D79E4C5EC1162 ON grade_h (cycle_id)');
        $this->addSql('CREATE INDEX IDX_B5D79E4CD1AAB236 ON grade_h (bulletin_id)');
        $this->addSql('CREATE TABLE grade_p (id SERIAL NOT NULL, bulletin_id INT NOT NULL, matiere_id INT NOT NULL, trimester_id INT NOT NULL, note INT NOT NULL, mois INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A6BB061AD1AAB236 ON grade_p (bulletin_id)');
        $this->addSql('CREATE INDEX IDX_A6BB061AF46CD258 ON grade_p (matiere_id)');
        $this->addSql('CREATE INDEX IDX_A6BB061AA6D57929 ON grade_p (trimester_id)');
        $this->addSql('CREATE TABLE matiere (id SERIAL NOT NULL, professeur_id INT DEFAULT NULL, classe_id INT DEFAULT NULL, bulletin_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, coefficient INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9014574ABAB22EE9 ON matiere (professeur_id)');
        $this->addSql('CREATE INDEX IDX_9014574A8F5EA509 ON matiere (classe_id)');
        $this->addSql('CREATE INDEX IDX_9014574AD1AAB236 ON matiere (bulletin_id)');
        $this->addSql('CREATE TABLE moyenne (id SERIAL NOT NULL, eleve_id INT DEFAULT NULL, cycle_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F27AFF8FA6CC7B2 ON moyenne (eleve_id)');
        $this->addSql('CREATE INDEX IDX_F27AFF8F5EC1162 ON moyenne (cycle_id)');
        $this->addSql('CREATE TABLE professeur (id SERIAL NOT NULL, ecole_id INT DEFAULT NULL, classe_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_17A5529977EF1B1E ON professeur (ecole_id)');
        $this->addSql('CREATE INDEX IDX_17A552998F5EA509 ON professeur (classe_id)');
        $this->addSql('CREATE TABLE trimester (id SERIAL NOT NULL, classe_id INT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D256BD198F5EA509 ON trimester (classe_id)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('ALTER TABLE bulletin ADD CONSTRAINT FK_2B7D8942A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bulletin ADD CONSTRAINT FK_2B7D89428F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bulletin ADD CONSTRAINT FK_2B7D894277EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF9677EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cycle ADD CONSTRAINT FK_B086D193D1AAB236 FOREIGN KEY (bulletin_id) REFERENCES bulletin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cycle ADD CONSTRAINT FK_B086D193A6D57929 FOREIGN KEY (trimester_id) REFERENCES trimester (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecole ADD CONSTRAINT FK_9786AACFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F78F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F777EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE34A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE34F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE34D1AAB236 FOREIGN KEY (bulletin_id) REFERENCES bulletin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE34A6D57929 FOREIGN KEY (trimester_id) REFERENCES trimester (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade_h ADD CONSTRAINT FK_B5D79E4CF46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade_h ADD CONSTRAINT FK_B5D79E4CA6D57929 FOREIGN KEY (trimester_id) REFERENCES trimester (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade_h ADD CONSTRAINT FK_B5D79E4C5EC1162 FOREIGN KEY (cycle_id) REFERENCES cycle (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade_h ADD CONSTRAINT FK_B5D79E4CD1AAB236 FOREIGN KEY (bulletin_id) REFERENCES bulletin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade_p ADD CONSTRAINT FK_A6BB061AD1AAB236 FOREIGN KEY (bulletin_id) REFERENCES bulletin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade_p ADD CONSTRAINT FK_A6BB061AF46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade_p ADD CONSTRAINT FK_A6BB061AA6D57929 FOREIGN KEY (trimester_id) REFERENCES trimester (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE matiere ADD CONSTRAINT FK_9014574ABAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE matiere ADD CONSTRAINT FK_9014574A8F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE matiere ADD CONSTRAINT FK_9014574AD1AAB236 FOREIGN KEY (bulletin_id) REFERENCES bulletin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE moyenne ADD CONSTRAINT FK_F27AFF8FA6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE moyenne ADD CONSTRAINT FK_F27AFF8F5EC1162 FOREIGN KEY (cycle_id) REFERENCES cycle (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE professeur ADD CONSTRAINT FK_17A5529977EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE professeur ADD CONSTRAINT FK_17A552998F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trimester ADD CONSTRAINT FK_D256BD198F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bulletin DROP CONSTRAINT FK_2B7D8942A6CC7B2');
        $this->addSql('ALTER TABLE bulletin DROP CONSTRAINT FK_2B7D89428F5EA509');
        $this->addSql('ALTER TABLE bulletin DROP CONSTRAINT FK_2B7D894277EF1B1E');
        $this->addSql('ALTER TABLE classe DROP CONSTRAINT FK_8F87BF9677EF1B1E');
        $this->addSql('ALTER TABLE cycle DROP CONSTRAINT FK_B086D193D1AAB236');
        $this->addSql('ALTER TABLE cycle DROP CONSTRAINT FK_B086D193A6D57929');
        $this->addSql('ALTER TABLE ecole DROP CONSTRAINT FK_9786AACFB88E14F');
        $this->addSql('ALTER TABLE eleve DROP CONSTRAINT FK_ECA105F78F5EA509');
        $this->addSql('ALTER TABLE eleve DROP CONSTRAINT FK_ECA105F777EF1B1E');
        $this->addSql('ALTER TABLE grade DROP CONSTRAINT FK_595AAE34A6CC7B2');
        $this->addSql('ALTER TABLE grade DROP CONSTRAINT FK_595AAE34F46CD258');
        $this->addSql('ALTER TABLE grade DROP CONSTRAINT FK_595AAE34D1AAB236');
        $this->addSql('ALTER TABLE grade DROP CONSTRAINT FK_595AAE34A6D57929');
        $this->addSql('ALTER TABLE grade_h DROP CONSTRAINT FK_B5D79E4CF46CD258');
        $this->addSql('ALTER TABLE grade_h DROP CONSTRAINT FK_B5D79E4CA6D57929');
        $this->addSql('ALTER TABLE grade_h DROP CONSTRAINT FK_B5D79E4C5EC1162');
        $this->addSql('ALTER TABLE grade_h DROP CONSTRAINT FK_B5D79E4CD1AAB236');
        $this->addSql('ALTER TABLE grade_p DROP CONSTRAINT FK_A6BB061AD1AAB236');
        $this->addSql('ALTER TABLE grade_p DROP CONSTRAINT FK_A6BB061AF46CD258');
        $this->addSql('ALTER TABLE grade_p DROP CONSTRAINT FK_A6BB061AA6D57929');
        $this->addSql('ALTER TABLE matiere DROP CONSTRAINT FK_9014574ABAB22EE9');
        $this->addSql('ALTER TABLE matiere DROP CONSTRAINT FK_9014574A8F5EA509');
        $this->addSql('ALTER TABLE matiere DROP CONSTRAINT FK_9014574AD1AAB236');
        $this->addSql('ALTER TABLE moyenne DROP CONSTRAINT FK_F27AFF8FA6CC7B2');
        $this->addSql('ALTER TABLE moyenne DROP CONSTRAINT FK_F27AFF8F5EC1162');
        $this->addSql('ALTER TABLE professeur DROP CONSTRAINT FK_17A5529977EF1B1E');
        $this->addSql('ALTER TABLE professeur DROP CONSTRAINT FK_17A552998F5EA509');
        $this->addSql('ALTER TABLE trimester DROP CONSTRAINT FK_D256BD198F5EA509');
        $this->addSql('DROP TABLE bulletin');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE cycle');
        $this->addSql('DROP TABLE ecole');
        $this->addSql('DROP TABLE eleve');
        $this->addSql('DROP TABLE grade');
        $this->addSql('DROP TABLE grade_h');
        $this->addSql('DROP TABLE grade_p');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('DROP TABLE moyenne');
        $this->addSql('DROP TABLE professeur');
        $this->addSql('DROP TABLE trimester');
        $this->addSql('DROP TABLE "user"');
    }
}
