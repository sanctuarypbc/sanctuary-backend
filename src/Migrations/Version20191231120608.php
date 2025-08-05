<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191231120608 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE organization_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization (id INT AUTO_INCREMENT NOT NULL, organization_type_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, contact_name VARCHAR(255) DEFAULT NULL, contact_phone VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_C1EE637C89E04D0 (organization_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE advocate_detail (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, user_id INT NOT NULL, identifier VARCHAR(255) DEFAULT NULL, additional_phone VARCHAR(255) DEFAULT NULL, emergency_contact VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_71BE29E132C8A3DE (organization_id), UNIQUE INDEX UNIQ_71BE29E1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE advocate_detail_language (advocate_detail_id INT NOT NULL, language_id INT NOT NULL, INDEX IDX_815B3299A6FE7EB6 (advocate_detail_id), INDEX IDX_815B329982F1BAF4 (language_id), PRIMARY KEY(advocate_detail_id, language_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facility (id INT AUTO_INCREMENT NOT NULL, facility_type_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, contact_name VARCHAR(255) DEFAULT NULL, contact_phone VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, available_beds INT DEFAULT NULL, dependents_allowed TINYINT(1) DEFAULT NULL, pets_allowed TINYINT(1) DEFAULT NULL, hours_of_operation VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_105994B2753120A6 (facility_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE first_responder_detail (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, organization_id INT DEFAULT NULL, office_phone VARCHAR(255) DEFAULT NULL, identification_number VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, UNIQUE INDEX UNIQ_E6FC46B1A76ED395 (user_id), INDEX IDX_E6FC46B132C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_detail (id INT AUTO_INCREMENT NOT NULL, first_responder_id INT DEFAULT NULL, advocate_id INT DEFAULT NULL, user_id INT NOT NULL, identifier VARCHAR(255) DEFAULT NULL, contact_phone VARCHAR(255) DEFAULT NULL, safe_location VARCHAR(255) DEFAULT NULL, emergency_contact VARCHAR(255) DEFAULT NULL, employment_status TINYINT(1) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, pet_status VARCHAR(255) DEFAULT NULL, age VARCHAR(255) DEFAULT NULL, clothing_size VARCHAR(255) DEFAULT NULL, shoe_size VARCHAR(255) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, date_assisted DATETIME DEFAULT NULL, date_assigned DATETIME DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_7BA0342042CD0C4A (first_responder_id), INDEX IDX_7BA0342048FF83CD (advocate_id), UNIQUE INDEX UNIQ_7BA03420A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dependent (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, client_detail_id INT DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, parent VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, age VARCHAR(255) DEFAULT NULL, clothing_size VARCHAR(255) DEFAULT NULL, shoe_size VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_BB9077A4A76ED395 (user_id), INDEX IDX_BB9077A433F0D3B4 (client_detail_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facility_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637C89E04D0 FOREIGN KEY (organization_type_id) REFERENCES organization_type (id)');
        $this->addSql('ALTER TABLE advocate_detail ADD CONSTRAINT FK_71BE29E132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE advocate_detail ADD CONSTRAINT FK_71BE29E1A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE advocate_detail_language ADD CONSTRAINT FK_815B3299A6FE7EB6 FOREIGN KEY (advocate_detail_id) REFERENCES advocate_detail (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE advocate_detail_language ADD CONSTRAINT FK_815B329982F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE facility ADD CONSTRAINT FK_105994B2753120A6 FOREIGN KEY (facility_type_id) REFERENCES facility_type (id)');
        $this->addSql('ALTER TABLE first_responder_detail ADD CONSTRAINT FK_E6FC46B1A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE first_responder_detail ADD CONSTRAINT FK_E6FC46B132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA0342042CD0C4A FOREIGN KEY (first_responder_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA0342048FF83CD FOREIGN KEY (advocate_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA03420A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE dependent ADD CONSTRAINT FK_BB9077A4A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE dependent ADD CONSTRAINT FK_BB9077A433F0D3B4 FOREIGN KEY (client_detail_id) REFERENCES client_detail (id)');
        $this->addSql('ALTER TABLE fos_user ADD client_advocate_detail_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A6479CC6415A6 FOREIGN KEY (client_advocate_detail_id) REFERENCES advocate_detail (id)');
        $this->addSql('CREATE INDEX IDX_957A6479CC6415A6 ON fos_user (client_advocate_detail_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637C89E04D0');
        $this->addSql('ALTER TABLE advocate_detail DROP FOREIGN KEY FK_71BE29E132C8A3DE');
        $this->addSql('ALTER TABLE first_responder_detail DROP FOREIGN KEY FK_E6FC46B132C8A3DE');
        $this->addSql('ALTER TABLE advocate_detail_language DROP FOREIGN KEY FK_815B3299A6FE7EB6');
        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A6479CC6415A6');
        $this->addSql('ALTER TABLE dependent DROP FOREIGN KEY FK_BB9077A433F0D3B4');
        $this->addSql('ALTER TABLE advocate_detail_language DROP FOREIGN KEY FK_815B329982F1BAF4');
        $this->addSql('ALTER TABLE facility DROP FOREIGN KEY FK_105994B2753120A6');
        $this->addSql('DROP TABLE organization_type');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE advocate_detail');
        $this->addSql('DROP TABLE advocate_detail_language');
        $this->addSql('DROP TABLE facility');
        $this->addSql('DROP TABLE first_responder_detail');
        $this->addSql('DROP TABLE client_detail');
        $this->addSql('DROP TABLE dependent');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE facility_type');
        $this->addSql('DROP INDEX IDX_957A6479CC6415A6 ON fos_user');
        $this->addSql('ALTER TABLE fos_user DROP client_advocate_detail_id');
    }
}
