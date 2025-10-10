<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251008184857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bulletin (id SERIAL NOT NULL, eleve_id INT NOT NULL, classe_id INT NOT NULL, trimester_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2B7D8942A6CC7B2 ON bulletin (eleve_id)');
        $this->addSql('CREATE INDEX IDX_2B7D89428F5EA509 ON bulletin (classe_id)');
        $this->addSql('CREATE INDEX IDX_2B7D8942A6D57929 ON bulletin (trimester_id)');
        $this->addSql('CREATE TABLE classe (id SERIAL NOT NULL, ecole_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, niveau VARCHAR(255) NOT NULL, frais INT NOT NULL, nb_max INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F87BF9677EF1B1E ON classe (ecole_id)');
        $this->addSql('CREATE TABLE ecole (id SERIAL NOT NULL, utilisateur_id INT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9786AACFB88E14F ON ecole (utilisateur_id)');
        $this->addSql('CREATE TABLE eleve (id SERIAL NOT NULL, classe_id INT DEFAULT NULL, ecole_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, birthday VARCHAR(255) NOT NULL, solde_initial INT NOT NULL, email_parent VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ECA105F78F5EA509 ON eleve (classe_id)');
        $this->addSql('CREATE INDEX IDX_ECA105F777EF1B1E ON eleve (ecole_id)');
        $this->addSql('CREATE TABLE grade (id SERIAL NOT NULL, eleve_id INT NOT NULL, matiere_id INT NOT NULL, bulletin_id INT DEFAULT NULL, trimester_id INT NOT NULL, note INT NOT NULL, note_maximal INT NOT NULL, type_examen VARCHAR(255) NOT NULL, date VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_595AAE34A6CC7B2 ON grade (eleve_id)');
        $this->addSql('CREATE INDEX IDX_595AAE34F46CD258 ON grade (matiere_id)');
        $this->addSql('CREATE INDEX IDX_595AAE34D1AAB236 ON grade (bulletin_id)');
        $this->addSql('CREATE INDEX IDX_595AAE34A6D57929 ON grade (trimester_id)');
        $this->addSql('CREATE TABLE matiere (id SERIAL NOT NULL, professeur_id INT DEFAULT NULL, classe_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, coefficient INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9014574ABAB22EE9 ON matiere (professeur_id)');
        $this->addSql('CREATE INDEX IDX_9014574A8F5EA509 ON matiere (classe_id)');
        $this->addSql('CREATE TABLE professeur (id SERIAL NOT NULL, ecole_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_17A5529977EF1B1E ON professeur (ecole_id)');
        $this->addSql('CREATE TABLE trimester (id SERIAL NOT NULL, classe_id INT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D256BD198F5EA509 ON trimester (classe_id)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('ALTER TABLE bulletin ADD CONSTRAINT FK_2B7D8942A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bulletin ADD CONSTRAINT FK_2B7D89428F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bulletin ADD CONSTRAINT FK_2B7D8942A6D57929 FOREIGN KEY (trimester_id) REFERENCES trimester (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF9677EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecole ADD CONSTRAINT FK_9786AACFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F78F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F777EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE34A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE34F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE34D1AAB236 FOREIGN KEY (bulletin_id) REFERENCES bulletin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE34A6D57929 FOREIGN KEY (trimester_id) REFERENCES trimester (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE matiere ADD CONSTRAINT FK_9014574ABAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE matiere ADD CONSTRAINT FK_9014574A8F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE professeur ADD CONSTRAINT FK_17A5529977EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trimester ADD CONSTRAINT FK_D256BD198F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bulletin DROP CONSTRAINT FK_2B7D8942A6CC7B2');
        $this->addSql('ALTER TABLE bulletin DROP CONSTRAINT FK_2B7D89428F5EA509');
        $this->addSql('ALTER TABLE bulletin DROP CONSTRAINT FK_2B7D8942A6D57929');
        $this->addSql('ALTER TABLE classe DROP CONSTRAINT FK_8F87BF9677EF1B1E');
        $this->addSql('ALTER TABLE ecole DROP CONSTRAINT FK_9786AACFB88E14F');
        $this->addSql('ALTER TABLE eleve DROP CONSTRAINT FK_ECA105F78F5EA509');
        $this->addSql('ALTER TABLE eleve DROP CONSTRAINT FK_ECA105F777EF1B1E');
        $this->addSql('ALTER TABLE grade DROP CONSTRAINT FK_595AAE34A6CC7B2');
        $this->addSql('ALTER TABLE grade DROP CONSTRAINT FK_595AAE34F46CD258');
        $this->addSql('ALTER TABLE grade DROP CONSTRAINT FK_595AAE34D1AAB236');
        $this->addSql('ALTER TABLE grade DROP CONSTRAINT FK_595AAE34A6D57929');
        $this->addSql('ALTER TABLE matiere DROP CONSTRAINT FK_9014574ABAB22EE9');
        $this->addSql('ALTER TABLE matiere DROP CONSTRAINT FK_9014574A8F5EA509');
        $this->addSql('ALTER TABLE professeur DROP CONSTRAINT FK_17A5529977EF1B1E');
        $this->addSql('ALTER TABLE trimester DROP CONSTRAINT FK_D256BD198F5EA509');
        $this->addSql('DROP TABLE bulletin');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE ecole');
        $this->addSql('DROP TABLE eleve');
        $this->addSql('DROP TABLE grade');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('DROP TABLE professeur');
        $this->addSql('DROP TABLE trimester');
        $this->addSql('DROP TABLE "user"');
    }
}
