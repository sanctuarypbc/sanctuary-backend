<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200313104942 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE facility_inventory_type (id INT AUTO_INCREMENT NOT NULL, facility_id INT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_4ECDA983A7014910 (facility_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facility_inventory (id INT AUTO_INCREMENT NOT NULL, inventory_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, capacity INT DEFAULT NULL, total_available INT DEFAULT NULL, availability_update_at DATETIME DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_CC7CD73E84482A6F (inventory_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facility_inventory_type ADD CONSTRAINT FK_4ECDA983A7014910 FOREIGN KEY (facility_id) REFERENCES facility (id)');
        $this->addSql('ALTER TABLE facility_inventory ADD CONSTRAINT FK_CC7CD73E84482A6F FOREIGN KEY (inventory_type_id) REFERENCES facility_inventory_type (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE facility_inventory DROP FOREIGN KEY FK_CC7CD73E84482A6F');
        $this->addSql('DROP TABLE facility_inventory_type');
        $this->addSql('DROP TABLE facility_inventory');
    }
}
