<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200930065024 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE booking (id INT UNSIGNED AUTO_INCREMENT NOT NULL, performed_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, facility_id INT UNSIGNED NOT NULL, facility_inventory_type_id INT UNSIGNED NOT NULL, check_in DATETIME DEFAULT NULL, check_out DATETIME DEFAULT NULL, notes LONGTEXT DEFAULT NULL, room_number VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_E00CEDDE19193BB0 (performed_id), INDEX IDX_E00CEDDEA76ED395 (user_id), INDEX IDX_E00CEDDEA7014910 (facility_id), INDEX IDX_E00CEDDE1F22D90A (facility_inventory_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE19193BB0 FOREIGN KEY (performed_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA7014910 FOREIGN KEY (facility_id) REFERENCES facility (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE1F22D90A FOREIGN KEY (facility_inventory_type_id) REFERENCES facility_inventory_type (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE booking');
    }
}
