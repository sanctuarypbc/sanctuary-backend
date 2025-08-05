<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200830123049 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE covid_answer (id INT AUTO_INCREMENT NOT NULL, client_detail_id INT NOT NULL, covid_question_id INT NOT NULL, value LONGTEXT NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_68104D033F0D3B4 (client_detail_id), INDEX IDX_68104D0AD135B5B (covid_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE covid_question (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE covid_answer ADD CONSTRAINT FK_68104D033F0D3B4 FOREIGN KEY (client_detail_id) REFERENCES client_detail (id)');
        $this->addSql('ALTER TABLE covid_answer ADD CONSTRAINT FK_68104D0AD135B5B FOREIGN KEY (covid_question_id) REFERENCES covid_question (id)');
        $this->addSql('ALTER TABLE client_detail ADD phone_with_cellular_service TINYINT(1) DEFAULT NULL, ADD need_translator TINYINT(1) DEFAULT NULL, ADD case_number VARCHAR(255) DEFAULT NULL, ADD date_of_incident DATETIME DEFAULT NULL, ADD valid_id TINYINT(1) DEFAULT NULL, ADD abuser_location VARCHAR(255) DEFAULT NULL,ADD client_address VARCHAR(255) DEFAULT NULL ,ADD client_address_type VARCHAR(255) DEFAULT NULL, ADD number_of_pets INT DEFAULT NULL, ADD physically_disabled VARCHAR(255) DEFAULT NULL, ADD need_medical_assistance TINYINT(1) DEFAULT NULL, ADD contacted_family TINYINT(1) DEFAULT NULL, ADD is_waitlisted TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE covid_answer DROP FOREIGN KEY FK_68104D0AD135B5B');
        $this->addSql('DROP TABLE covid_answer');
        $this->addSql('DROP TABLE covid_question');
        $this->addSql('ALTER TABLE client_detail DROP phone_with_cellular_service, DROP need_translator, DROP case_number, DROP date_of_incident, DROP valid_id, DROP abuser_location, DROP number_of_pets, DROP physically_disabled, DROP need_medical_assistance, DROP contacted_family, DROP client_address, DROP client_address_type, DROP is_waitlisted');
    }
}
