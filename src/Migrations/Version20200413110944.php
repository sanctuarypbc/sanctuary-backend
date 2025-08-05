<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200413110944 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE client_employment_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client_detail ADD client_employment_status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA034204DCC3C70 FOREIGN KEY (client_employment_status_id) REFERENCES client_employment_status (id)');
        $this->addSql('CREATE INDEX IDX_7BA034204DCC3C70 ON client_detail (client_employment_status_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA034204DCC3C70');
        $this->addSql('DROP TABLE client_employment_status');
        $this->addSql('DROP INDEX IDX_7BA034204DCC3C70 ON client_detail');
        $this->addSql('ALTER TABLE client_detail DROP client_employment_status_id');
    }
}
