<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200422003445 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin_client (admin_id INT NOT NULL, client_id INT NOT NULL, INDEX IDX_9A8C35AC642B8210 (admin_id), INDEX IDX_9A8C35AC19EB6921 (client_id), PRIMARY KEY(admin_id, client_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin_proprietaire (admin_id INT NOT NULL, proprietaire_id INT NOT NULL, INDEX IDX_10A23B3B642B8210 (admin_id), INDEX IDX_10A23B3B76C50E4A (proprietaire_id), PRIMARY KEY(admin_id, proprietaire_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin_client ADD CONSTRAINT FK_9A8C35AC642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_client ADD CONSTRAINT FK_9A8C35AC19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_proprietaire ADD CONSTRAINT FK_10A23B3B642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_proprietaire ADD CONSTRAINT FK_10A23B3B76C50E4A FOREIGN KEY (proprietaire_id) REFERENCES proprietaire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categorie CHANGE nom nom VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE commande CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE details_commande CHANGE commande_id commande_id INT DEFAULT NULL, CHANGE produit_id produit_id INT DEFAULT NULL, CHANGE quantite quantite INT DEFAULT NULL');
        $this->addSql('ALTER TABLE garde CHANGE date_debut date_debut DATE DEFAULT NULL, CHANGE date_fin date_fin DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE produit CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE date_expiration date_expiration DATE DEFAULT NULL, CHANGE prix_ht prix_ht DOUBLE PRECISION DEFAULT NULL, CHANGE prix_tva prix_tva DOUBLE PRECISION DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE stock CHANGE proprietaire_id proprietaire_id INT DEFAULT NULL, CHANGE produit_id produit_id INT DEFAULT NULL, CHANGE quantite quantite INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin_client DROP FOREIGN KEY FK_9A8C35AC642B8210');
        $this->addSql('ALTER TABLE admin_proprietaire DROP FOREIGN KEY FK_10A23B3B642B8210');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE admin_client');
        $this->addSql('DROP TABLE admin_proprietaire');
        $this->addSql('ALTER TABLE categorie CHANGE nom nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE commande CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE details_commande CHANGE commande_id commande_id INT DEFAULT NULL, CHANGE produit_id produit_id INT DEFAULT NULL, CHANGE quantite quantite INT DEFAULT NULL');
        $this->addSql('ALTER TABLE garde CHANGE date_debut date_debut DATE DEFAULT \'NULL\', CHANGE date_fin date_fin DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE produit CHANGE nom nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE date_expiration date_expiration DATE DEFAULT \'NULL\', CHANGE prix_ht prix_ht DOUBLE PRECISION DEFAULT \'NULL\', CHANGE prix_tva prix_tva DOUBLE PRECISION DEFAULT \'NULL\', CHANGE image image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE stock CHANGE proprietaire_id proprietaire_id INT DEFAULT NULL, CHANGE produit_id produit_id INT DEFAULT NULL, CHANGE quantite quantite INT DEFAULT NULL');
    }
}
