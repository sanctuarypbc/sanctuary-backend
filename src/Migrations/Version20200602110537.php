<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200602110537 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE client_inventory_assignment (id INT AUTO_INCREMENT NOT NULL, client_detail_id INT NOT NULL, facility_inventory_id INT NOT NULL, assigned_at DATETIME NOT NULL, check_in_at DATETIME DEFAULT NULL, check_out_at DATETIME DEFAULT NULL, quantity INT NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_E389D12233F0D3B4 (client_detail_id), INDEX IDX_E389D122DF7E63A9 (facility_inventory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client_inventory_assignment ADD CONSTRAINT FK_E389D12233F0D3B4 FOREIGN KEY (client_detail_id) REFERENCES client_detail (id)');
        $this->addSql('ALTER TABLE client_inventory_assignment ADD CONSTRAINT FK_E389D122DF7E63A9 FOREIGN KEY (facility_inventory_id) REFERENCES facility_inventory (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE client_inventory_assignment');
    }
}
