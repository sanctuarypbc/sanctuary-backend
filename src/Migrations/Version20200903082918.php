<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200903082918 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET FOREIGN_KEY_CHECKS=0;');
        $this->addSql('Drop table access_token');
        $this->addSql('Drop table advocate_detail');
        $this->addSql('Drop table advocate_detail_language');
        $this->addSql('Drop table advocate_service_type');
        $this->addSql('Drop table auth_code');
        $this->addSql('Drop table client');
        $this->addSql('Drop table client_detail');
        $this->addSql('Drop table client_employment_status');
        $this->addSql('Drop table client_goal');
        $this->addSql('Drop table client_goal_task');
        $this->addSql('Drop table client_inventory_assignment');
        $this->addSql('Drop table client_occupation');
        $this->addSql('Drop table client_request');
        $this->addSql('Drop table client_request_comment');
        $this->addSql('Drop table client_status');
        $this->addSql('Drop table client_type');
        $this->addSql('Drop table covid_answer');
        $this->addSql('Drop table covid_question');
        $this->addSql('Drop table dependent');
        $this->addSql('Drop table facility');
        $this->addSql('Drop table facility_inventory');
        $this->addSql('Drop table facility_inventory_type');
        $this->addSql('Drop table facility_type');
        $this->addSql('Drop table facility_waitlist');
        $this->addSql('Drop table first_responder_detail');
        $this->addSql('Drop table first_responder_type');
        $this->addSql('Drop table fos_user');
        $this->addSql('Drop table goal');
        $this->addSql('Drop table language');
        $this->addSql('Drop table log_entries');
        $this->addSql('Drop table newsfeed');
        $this->addSql('Drop table newsfeed_file');
        $this->addSql('Drop table organization');
        $this->addSql('Drop table organization_client_type');
        $this->addSql('Drop table organization_type');
        $this->addSql('Drop table refresh_token');
        $this->addSql('Drop table request');
        $this->addSql('Drop table reset_token');
        $this->addSql('Drop table task');
        $this->addSql('Drop table user_address');


        $this->addSql('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET FOREIGN_KEY_CHECKS=0;');
        $this->addSql('ALTER TABLE access_token CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE advocate_detail CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE organization_id organization_id INT DEFAULT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE service_type_id service_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE advocate_detail_language CHANGE advocate_detail_id advocate_detail_id INT NOT NULL, CHANGE language_id language_id INT NOT NULL');
        $this->addSql('ALTER TABLE advocate_service_type CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE auth_code CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client_detail CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE first_responder_id first_responder_id INT DEFAULT NULL, CHANGE advocate_id advocate_id INT DEFAULT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE client_type_id client_type_id INT DEFAULT NULL, CHANGE client_status_id client_status_id INT DEFAULT NULL, CHANGE client_occupation_id client_occupation_id INT DEFAULT NULL, CHANGE facility_id facility_id INT DEFAULT NULL, CHANGE client_employment_status_id client_employment_status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client_employment_status CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE client_goal CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE client_detail_id client_detail_id INT NOT NULL, CHANGE goal_id goal_id INT NOT NULL, CHANGE assigned_by_id assigned_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE client_goal_task CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE task_id task_id INT NOT NULL, CHANGE client_goal_id client_goal_id INT NOT NULL');
        $this->addSql('ALTER TABLE client_inventory_assignment CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE client_detail_id client_detail_id INT NOT NULL, CHANGE facility_inventory_id facility_inventory_id INT NOT NULL');
        $this->addSql('ALTER TABLE client_occupation CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE client_request CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE client_detail_id client_detail_id INT NOT NULL, CHANGE request_id request_id INT NOT NULL');
        $this->addSql('ALTER TABLE client_request_comment CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE client_request_id client_request_id INT NOT NULL');
        $this->addSql('ALTER TABLE client_status CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE client_type CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE covid_answer CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE client_detail_id client_detail_id INT NOT NULL, CHANGE covid_question_id covid_question_id INT NOT NULL');
        $this->addSql('ALTER TABLE covid_question CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE dependent CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE client_detail_id client_detail_id INT NOT NULL');
        $this->addSql('ALTER TABLE facility CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE facility_type_id facility_type_id INT NOT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE facility_inventory CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE inventory_type_id inventory_type_id INT NOT NULL, CHANGE facility_id facility_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE facility_inventory_type CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE facility_id facility_id INT NOT NULL');
        $this->addSql('ALTER TABLE facility_type CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE facility_waitlist CHANGE user_id user_id INT NOT NULL, CHANGE facility_id facility_id INT NOT NULL');
        $this->addSql('ALTER TABLE first_responder_detail CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE organization_id organization_id INT DEFAULT NULL, CHANGE first_responder_type_id first_responder_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE first_responder_type CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE fos_user CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE goal CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE language CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE log_entries CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE newsfeed CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE newsfeed_file CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE news_feed_id news_feed_id INT NOT NULL');
        $this->addSql('ALTER TABLE organization CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE organization_type_id organization_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE organization_client_type CHANGE organization_id organization_id INT NOT NULL, CHANGE client_type_id client_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE organization_type CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE refresh_token CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE request CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE reset_token CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE task CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE goal_id goal_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_address CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('SET FOREIGN_KEY_CHECKS=1;');
    }
}
