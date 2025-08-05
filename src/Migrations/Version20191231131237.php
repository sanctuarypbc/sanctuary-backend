<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191231131237 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE advocate_service_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE first_responder_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_occupation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE advocate_detail ADD service_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE advocate_detail ADD CONSTRAINT FK_71BE29E1AC8DE0F FOREIGN KEY (service_type_id) REFERENCES advocate_service_type (id)');
        $this->addSql('CREATE INDEX IDX_71BE29E1AC8DE0F ON advocate_detail (service_type_id)');
        $this->addSql('ALTER TABLE first_responder_detail ADD first_responder_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE first_responder_detail ADD CONSTRAINT FK_E6FC46B110E4374E FOREIGN KEY (first_responder_type_id) REFERENCES first_responder_type (id)');
        $this->addSql('CREATE INDEX IDX_E6FC46B110E4374E ON first_responder_detail (first_responder_type_id)');
        $this->addSql('ALTER TABLE client_detail ADD client_type_id INT DEFAULT NULL, ADD client_status_id INT DEFAULT NULL, ADD client_occupation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA034209771C8EE FOREIGN KEY (client_type_id) REFERENCES client_type (id)');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA0342080D7D0B2 FOREIGN KEY (client_status_id) REFERENCES client_status (id)');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA03420557EC614 FOREIGN KEY (client_occupation_id) REFERENCES client_occupation (id)');
        $this->addSql('CREATE INDEX IDX_7BA034209771C8EE ON client_detail (client_type_id)');
        $this->addSql('CREATE INDEX IDX_7BA0342080D7D0B2 ON client_detail (client_status_id)');
        $this->addSql('CREATE INDEX IDX_7BA03420557EC614 ON client_detail (client_occupation_id)');
        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A6479CC6415A6');
        $this->addSql('DROP INDEX IDX_957A6479CC6415A6 ON fos_user');
        $this->addSql('ALTER TABLE fos_user ADD phone VARCHAR(255) DEFAULT NULL, ADD dob VARCHAR(255) DEFAULT NULL, ADD gender VARCHAR(255) DEFAULT NULL, DROP client_advocate_detail_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE advocate_detail DROP FOREIGN KEY FK_71BE29E1AC8DE0F');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA0342080D7D0B2');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA034209771C8EE');
        $this->addSql('ALTER TABLE first_responder_detail DROP FOREIGN KEY FK_E6FC46B110E4374E');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA03420557EC614');
        $this->addSql('DROP TABLE advocate_service_type');
        $this->addSql('DROP TABLE client_status');
        $this->addSql('DROP TABLE client_type');
        $this->addSql('DROP TABLE first_responder_type');
        $this->addSql('DROP TABLE client_occupation');
        $this->addSql('DROP INDEX IDX_71BE29E1AC8DE0F ON advocate_detail');
        $this->addSql('ALTER TABLE advocate_detail DROP service_type_id');
        $this->addSql('DROP INDEX IDX_7BA034209771C8EE ON client_detail');
        $this->addSql('DROP INDEX IDX_7BA0342080D7D0B2 ON client_detail');
        $this->addSql('DROP INDEX IDX_7BA03420557EC614 ON client_detail');
        $this->addSql('ALTER TABLE client_detail DROP client_type_id, DROP client_status_id, DROP client_occupation_id');
        $this->addSql('DROP INDEX IDX_E6FC46B110E4374E ON first_responder_detail');
        $this->addSql('ALTER TABLE first_responder_detail DROP first_responder_type_id');
        $this->addSql('ALTER TABLE fos_user ADD client_advocate_detail_id INT DEFAULT NULL, DROP phone, DROP dob, DROP gender');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A6479CC6415A6 FOREIGN KEY (client_advocate_detail_id) REFERENCES advocate_detail (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_957A6479CC6415A6 ON fos_user (client_advocate_detail_id)');
    }
}
