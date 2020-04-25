<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200425173521 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D76A76ED395 ON admin (user_id)');
        $this->addSql('ALTER TABLE categorie CHANGE nom nom VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455A76ED395 ON client (user_id)');
        $this->addSql('ALTER TABLE commande CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE details_commande CHANGE commande_id commande_id INT DEFAULT NULL, CHANGE produit_id produit_id INT DEFAULT NULL, CHANGE quantite quantite INT DEFAULT NULL');
        $this->addSql('ALTER TABLE garde CHANGE date_debut date_debut DATE DEFAULT NULL, CHANGE date_fin date_fin DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE produit CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE date_expiration date_expiration DATE DEFAULT NULL, CHANGE prix_ht prix_ht DOUBLE PRECISION DEFAULT NULL, CHANGE prix_tva prix_tva DOUBLE PRECISION DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE proprietaire ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE proprietaire ADD CONSTRAINT FK_69E399D6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_69E399D6A76ED395 ON proprietaire (user_id)');
        $this->addSql('ALTER TABLE stock CHANGE proprietaire_id proprietaire_id INT DEFAULT NULL, CHANGE produit_id produit_id INT DEFAULT NULL, CHANGE quantite quantite INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE prenom prenom VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76A76ED395');
        $this->addSql('DROP INDEX UNIQ_880E0D76A76ED395 ON admin');
        $this->addSql('ALTER TABLE admin DROP user_id');
        $this->addSql('ALTER TABLE categorie CHANGE nom nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A76ED395');
        $this->addSql('DROP INDEX UNIQ_C7440455A76ED395 ON client');
        $this->addSql('ALTER TABLE client DROP user_id');
        $this->addSql('ALTER TABLE commande CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE details_commande CHANGE commande_id commande_id INT DEFAULT NULL, CHANGE produit_id produit_id INT DEFAULT NULL, CHANGE quantite quantite INT DEFAULT NULL');
        $this->addSql('ALTER TABLE garde CHANGE date_debut date_debut DATE DEFAULT \'NULL\', CHANGE date_fin date_fin DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE produit CHANGE nom nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE date_expiration date_expiration DATE DEFAULT \'NULL\', CHANGE prix_ht prix_ht DOUBLE PRECISION DEFAULT \'NULL\', CHANGE prix_tva prix_tva DOUBLE PRECISION DEFAULT \'NULL\', CHANGE image image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE proprietaire DROP FOREIGN KEY FK_69E399D6A76ED395');
        $this->addSql('DROP INDEX UNIQ_69E399D6A76ED395 ON proprietaire');
        $this->addSql('ALTER TABLE proprietaire DROP user_id');
        $this->addSql('ALTER TABLE stock CHANGE proprietaire_id proprietaire_id INT DEFAULT NULL, CHANGE produit_id produit_id INT DEFAULT NULL, CHANGE quantite quantite INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE nom nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE prenom prenom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
