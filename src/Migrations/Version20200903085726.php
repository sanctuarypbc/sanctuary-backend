<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200903085726 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fos_user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', phone VARCHAR(255) DEFAULT NULL, dob VARCHAR(255) DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, verification_code VARCHAR(255) DEFAULT NULL, verification_requested_at DATETIME DEFAULT NULL, agreed_terms TINYINT(1) DEFAULT NULL, agreed_terms_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facility_waitlist (user_id INT UNSIGNED NOT NULL, facility_id INT UNSIGNED NOT NULL, INDEX IDX_FE5086A7A76ED395 (user_id), INDEX IDX_FE5086A7A7014910 (facility_id), PRIMARY KEY(user_id, facility_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_occupation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_detail (id INT UNSIGNED AUTO_INCREMENT NOT NULL, first_responder_id INT UNSIGNED DEFAULT NULL, advocate_id INT UNSIGNED DEFAULT NULL, user_id INT UNSIGNED NOT NULL, client_type_id INT UNSIGNED DEFAULT NULL, client_status_id INT UNSIGNED DEFAULT NULL, client_occupation_id INT UNSIGNED DEFAULT NULL, facility_id INT UNSIGNED DEFAULT NULL, client_employment_status_id INT UNSIGNED DEFAULT NULL, identifier VARCHAR(255) DEFAULT NULL, contact_phone VARCHAR(255) DEFAULT NULL, safe_location VARCHAR(255) DEFAULT NULL, emergency_contact VARCHAR(255) DEFAULT NULL, employment_status TINYINT(1) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, pet_status VARCHAR(255) DEFAULT NULL, age VARCHAR(255) DEFAULT NULL, clothing_size VARCHAR(255) DEFAULT NULL, shoe_size VARCHAR(255) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, date_assisted DATETIME DEFAULT NULL, date_assigned DATETIME DEFAULT NULL, total_dependents INT DEFAULT NULL, phone_with_cellular_service TINYINT(1) DEFAULT NULL, need_translator TINYINT(1) DEFAULT NULL, case_number VARCHAR(255) DEFAULT NULL, date_of_incident DATETIME DEFAULT NULL, valid_id TINYINT(1) DEFAULT NULL, abuser_location VARCHAR(255) DEFAULT NULL, number_of_pets INT DEFAULT NULL, physically_disabled VARCHAR(255) DEFAULT NULL, need_medical_assistance TINYINT(1) DEFAULT NULL, contacted_family TINYINT(1) DEFAULT NULL, is_waitlisted TINYINT(1) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_7BA0342042CD0C4A (first_responder_id), INDEX IDX_7BA0342048FF83CD (advocate_id), UNIQUE INDEX UNIQ_7BA03420A76ED395 (user_id), INDEX IDX_7BA034209771C8EE (client_type_id), INDEX IDX_7BA0342080D7D0B2 (client_status_id), INDEX IDX_7BA03420557EC614 (client_occupation_id), INDEX IDX_7BA03420A7014910 (facility_id), INDEX IDX_7BA034204DCC3C70 (client_employment_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_goal (id INT UNSIGNED AUTO_INCREMENT NOT NULL, client_detail_id INT UNSIGNED NOT NULL, goal_id INT UNSIGNED NOT NULL, assigned_by_id INT UNSIGNED NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_2D40284C33F0D3B4 (client_detail_id), INDEX IDX_2D40284C667D1AFE (goal_id), INDEX IDX_2D40284C6E6F1246 (assigned_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_goal_task (id INT UNSIGNED AUTO_INCREMENT NOT NULL, task_id INT UNSIGNED NOT NULL, client_goal_id INT UNSIGNED NOT NULL, completed TINYINT(1) DEFAULT \'0\' NOT NULL, completed_at DATETIME DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_A76E4E888DB60186 (task_id), INDEX IDX_A76E4E8834405E83 (client_goal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE goal (id INT UNSIGNED AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsfeed (id INT UNSIGNED AUTO_INCREMENT NOT NULL, headline LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, show_to_client TINYINT(1) NOT NULL, link LONGTEXT DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_token (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, token VARCHAR(255) NOT NULL, expiry DATETIME NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_D7C8DC19A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE covid_question (id INT UNSIGNED AUTO_INCREMENT NOT NULL, text LONGTEXT NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE first_responder_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT UNSIGNED AUTO_INCREMENT NOT NULL, goal_id INT UNSIGNED NOT NULL, text LONGTEXT NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_527EDB25667D1AFE (goal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facility (id INT UNSIGNED AUTO_INCREMENT NOT NULL, facility_type_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, available_beds TINYINT(1) DEFAULT \'0\', dependents_allowed INT DEFAULT NULL, pets_allowed TINYINT(1) DEFAULT NULL, hours_of_operation VARCHAR(255) DEFAULT NULL, lat DOUBLE PRECISION DEFAULT NULL, lng DOUBLE PRECISION DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(255) DEFAULT NULL, opening_time VARCHAR(255) DEFAULT NULL, closing_time VARCHAR(255) DEFAULT NULL, work_all_day TINYINT(1) DEFAULT NULL, primary_color VARCHAR(255) DEFAULT NULL, secondary_color VARCHAR(255) DEFAULT NULL, url_prefix VARCHAR(255) DEFAULT NULL, desktop_logo VARCHAR(255) DEFAULT NULL, mobile_logo VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_105994B2753120A6 (facility_type_id), UNIQUE INDEX UNIQ_105994B2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE advocate_service_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facility_inventory (id INT UNSIGNED AUTO_INCREMENT NOT NULL, inventory_type_id INT UNSIGNED NOT NULL, facility_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, capacity INT DEFAULT NULL, total_available INT DEFAULT NULL, availability_update_at DATETIME DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_CC7CD73E84482A6F (inventory_type_id), INDEX IDX_CC7CD73EA7014910 (facility_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE advocate_detail (id INT UNSIGNED AUTO_INCREMENT NOT NULL, organization_id INT UNSIGNED DEFAULT NULL, user_id INT UNSIGNED NOT NULL, service_type_id INT UNSIGNED DEFAULT NULL, identifier VARCHAR(255) DEFAULT NULL, additional_phone VARCHAR(255) DEFAULT NULL, emergency_contact VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_71BE29E132C8A3DE (organization_id), UNIQUE INDEX UNIQ_71BE29E1A76ED395 (user_id), INDEX IDX_71BE29E1AC8DE0F (service_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE advocate_detail_language (advocate_detail_id INT UNSIGNED NOT NULL, language_id INT UNSIGNED NOT NULL, INDEX IDX_815B3299A6FE7EB6 (advocate_detail_id), INDEX IDX_815B329982F1BAF4 (language_id), PRIMARY KEY(advocate_detail_id, language_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE first_responder_detail (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, organization_id INT UNSIGNED DEFAULT NULL, first_responder_type_id INT UNSIGNED DEFAULT NULL, office_phone VARCHAR(255) DEFAULT NULL, identification_number VARCHAR(255) DEFAULT NULL, nick_name VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, UNIQUE INDEX UNIQ_E6FC46B1A76ED395 (user_id), INDEX IDX_E6FC46B132C8A3DE (organization_id), INDEX IDX_E6FC46B110E4374E (first_responder_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_address (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, street_address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, zip VARCHAR(255) NOT NULL, is_apartment TINYINT(1) NOT NULL, apartment_unit_number VARCHAR(255) DEFAULT NULL, address_state SMALLINT NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, UNIQUE INDEX UNIQ_5543718BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, title LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, is_default TINYINT(1) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_3B978F9FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_request (id INT UNSIGNED AUTO_INCREMENT NOT NULL, client_detail_id INT UNSIGNED NOT NULL, request_id INT UNSIGNED NOT NULL, status SMALLINT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_69AACBE233F0D3B4 (client_detail_id), INDEX IDX_69AACBE2427EB8A5 (request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facility_inventory_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, facility_id INT UNSIGNED NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_4ECDA983A7014910 (facility_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_status (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_inventory_assignment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, client_detail_id INT UNSIGNED NOT NULL, facility_inventory_id INT UNSIGNED NOT NULL, assigned_at DATETIME NOT NULL, check_in_at DATETIME DEFAULT NULL, check_out_at DATETIME DEFAULT NULL, quantity INT NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_E389D12233F0D3B4 (client_detail_id), INDEX IDX_E389D122DF7E63A9 (facility_inventory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsfeed_file (id INT UNSIGNED AUTO_INCREMENT NOT NULL, news_feed_id INT UNSIGNED NOT NULL, type VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_6BF63B76641A0E5 (news_feed_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_employment_status (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dependent (id INT UNSIGNED AUTO_INCREMENT NOT NULL, client_detail_id INT UNSIGNED NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, parent VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, age VARCHAR(255) DEFAULT NULL, clothing_size VARCHAR(255) DEFAULT NULL, shoe_size VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_BB9077A433F0D3B4 (client_detail_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE covid_answer (id INT UNSIGNED AUTO_INCREMENT NOT NULL, client_detail_id INT UNSIGNED NOT NULL, covid_question_id INT UNSIGNED NOT NULL, value LONGTEXT NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_68104D033F0D3B4 (client_detail_id), INDEX IDX_68104D0AD135B5B (covid_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_request_comment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, client_request_id INT UNSIGNED NOT NULL, text LONGTEXT DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_C3767DC8A76ED395 (user_id), INDEX IDX_C3767DC8D22A85E4 (client_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization (id INT UNSIGNED AUTO_INCREMENT NOT NULL, organization_type_id INT UNSIGNED NOT NULL, name VARCHAR(255) DEFAULT NULL, street_address VARCHAR(255) DEFAULT NULL, contact_name VARCHAR(255) DEFAULT NULL, contact_phone VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, lat DOUBLE PRECISION DEFAULT NULL, lng DOUBLE PRECISION DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(20) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_C1EE637C89E04D0 (organization_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization_client_type (organization_id INT UNSIGNED NOT NULL, client_type_id INT UNSIGNED NOT NULL, INDEX IDX_6D1E303032C8A3DE (organization_id), INDEX IDX_6D1E30309771C8EE (client_type_id), PRIMARY KEY(organization_id, client_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facility_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_entries (id INT AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, parent_id VARCHAR(64) DEFAULT NULL, parent_class VARCHAR(255) DEFAULT NULL, old_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', name VARCHAR(255) NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX IDX_15358B52A76ED395 (user_id), INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), INDEX log_entries_with_parent_lookup_idx (object_id, object_class, parent_id, parent_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE access_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_B6A2DD685F37A13B (token), INDEX IDX_B6A2DD6819EB6921 (client_id), INDEX IDX_B6A2DD68A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, random_id VARCHAR(255) NOT NULL, redirect_uris LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', secret VARCHAR(255) NOT NULL, allowed_grant_types LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_C74F21955F37A13B (token), INDEX IDX_C74F219519EB6921 (client_id), INDEX IDX_C74F2195A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auth_code (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, token VARCHAR(255) NOT NULL, redirect_uri LONGTEXT NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_5933D02C5F37A13B (token), INDEX IDX_5933D02C19EB6921 (client_id), INDEX IDX_5933D02CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facility_waitlist ADD CONSTRAINT FK_FE5086A7A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE facility_waitlist ADD CONSTRAINT FK_FE5086A7A7014910 FOREIGN KEY (facility_id) REFERENCES facility (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA0342042CD0C4A FOREIGN KEY (first_responder_id) REFERENCES first_responder_detail (id)');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA0342048FF83CD FOREIGN KEY (advocate_id) REFERENCES advocate_detail (id)');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA03420A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA034209771C8EE FOREIGN KEY (client_type_id) REFERENCES client_type (id)');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA0342080D7D0B2 FOREIGN KEY (client_status_id) REFERENCES client_status (id)');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA03420557EC614 FOREIGN KEY (client_occupation_id) REFERENCES client_occupation (id)');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA03420A7014910 FOREIGN KEY (facility_id) REFERENCES facility (id)');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA034204DCC3C70 FOREIGN KEY (client_employment_status_id) REFERENCES client_employment_status (id)');
        $this->addSql('ALTER TABLE client_goal ADD CONSTRAINT FK_2D40284C33F0D3B4 FOREIGN KEY (client_detail_id) REFERENCES client_detail (id)');
        $this->addSql('ALTER TABLE client_goal ADD CONSTRAINT FK_2D40284C667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id)');
        $this->addSql('ALTER TABLE client_goal ADD CONSTRAINT FK_2D40284C6E6F1246 FOREIGN KEY (assigned_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE client_goal_task ADD CONSTRAINT FK_A76E4E888DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE client_goal_task ADD CONSTRAINT FK_A76E4E8834405E83 FOREIGN KEY (client_goal_id) REFERENCES client_goal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_token ADD CONSTRAINT FK_D7C8DC19A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id)');
        $this->addSql('ALTER TABLE facility ADD CONSTRAINT FK_105994B2753120A6 FOREIGN KEY (facility_type_id) REFERENCES facility_type (id)');
        $this->addSql('ALTER TABLE facility ADD CONSTRAINT FK_105994B2A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE facility_inventory ADD CONSTRAINT FK_CC7CD73E84482A6F FOREIGN KEY (inventory_type_id) REFERENCES facility_inventory_type (id)');
        $this->addSql('ALTER TABLE facility_inventory ADD CONSTRAINT FK_CC7CD73EA7014910 FOREIGN KEY (facility_id) REFERENCES facility (id)');
        $this->addSql('ALTER TABLE advocate_detail ADD CONSTRAINT FK_71BE29E132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE advocate_detail ADD CONSTRAINT FK_71BE29E1A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE advocate_detail ADD CONSTRAINT FK_71BE29E1AC8DE0F FOREIGN KEY (service_type_id) REFERENCES advocate_service_type (id)');
        $this->addSql('ALTER TABLE advocate_detail_language ADD CONSTRAINT FK_815B3299A6FE7EB6 FOREIGN KEY (advocate_detail_id) REFERENCES advocate_detail (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE advocate_detail_language ADD CONSTRAINT FK_815B329982F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE first_responder_detail ADD CONSTRAINT FK_E6FC46B1A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE first_responder_detail ADD CONSTRAINT FK_E6FC46B132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE first_responder_detail ADD CONSTRAINT FK_E6FC46B110E4374E FOREIGN KEY (first_responder_type_id) REFERENCES first_responder_type (id)');
        $this->addSql('ALTER TABLE user_address ADD CONSTRAINT FK_5543718BA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE client_request ADD CONSTRAINT FK_69AACBE233F0D3B4 FOREIGN KEY (client_detail_id) REFERENCES client_detail (id)');
        $this->addSql('ALTER TABLE client_request ADD CONSTRAINT FK_69AACBE2427EB8A5 FOREIGN KEY (request_id) REFERENCES request (id)');
        $this->addSql('ALTER TABLE facility_inventory_type ADD CONSTRAINT FK_4ECDA983A7014910 FOREIGN KEY (facility_id) REFERENCES facility (id)');
        $this->addSql('ALTER TABLE client_inventory_assignment ADD CONSTRAINT FK_E389D12233F0D3B4 FOREIGN KEY (client_detail_id) REFERENCES client_detail (id)');
        $this->addSql('ALTER TABLE client_inventory_assignment ADD CONSTRAINT FK_E389D122DF7E63A9 FOREIGN KEY (facility_inventory_id) REFERENCES facility_inventory (id)');
        $this->addSql('ALTER TABLE newsfeed_file ADD CONSTRAINT FK_6BF63B76641A0E5 FOREIGN KEY (news_feed_id) REFERENCES newsfeed (id)');
        $this->addSql('ALTER TABLE dependent ADD CONSTRAINT FK_BB9077A433F0D3B4 FOREIGN KEY (client_detail_id) REFERENCES client_detail (id)');
        $this->addSql('ALTER TABLE covid_answer ADD CONSTRAINT FK_68104D033F0D3B4 FOREIGN KEY (client_detail_id) REFERENCES client_detail (id)');
        $this->addSql('ALTER TABLE covid_answer ADD CONSTRAINT FK_68104D0AD135B5B FOREIGN KEY (covid_question_id) REFERENCES covid_question (id)');
        $this->addSql('ALTER TABLE client_request_comment ADD CONSTRAINT FK_C3767DC8A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE client_request_comment ADD CONSTRAINT FK_C3767DC8D22A85E4 FOREIGN KEY (client_request_id) REFERENCES client_request (id)');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637C89E04D0 FOREIGN KEY (organization_type_id) REFERENCES organization_type (id)');
        $this->addSql('ALTER TABLE organization_client_type ADD CONSTRAINT FK_6D1E303032C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organization_client_type ADD CONSTRAINT FK_6D1E30309771C8EE FOREIGN KEY (client_type_id) REFERENCES client_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE log_entries ADD CONSTRAINT FK_15358B52A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD6819EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD68A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F219519EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F2195A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02CA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE facility_waitlist DROP FOREIGN KEY FK_FE5086A7A76ED395');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA03420A76ED395');
        $this->addSql('ALTER TABLE client_goal DROP FOREIGN KEY FK_2D40284C6E6F1246');
        $this->addSql('ALTER TABLE reset_token DROP FOREIGN KEY FK_D7C8DC19A76ED395');
        $this->addSql('ALTER TABLE facility DROP FOREIGN KEY FK_105994B2A76ED395');
        $this->addSql('ALTER TABLE advocate_detail DROP FOREIGN KEY FK_71BE29E1A76ED395');
        $this->addSql('ALTER TABLE first_responder_detail DROP FOREIGN KEY FK_E6FC46B1A76ED395');
        $this->addSql('ALTER TABLE user_address DROP FOREIGN KEY FK_5543718BA76ED395');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FA76ED395');
        $this->addSql('ALTER TABLE client_request_comment DROP FOREIGN KEY FK_C3767DC8A76ED395');
        $this->addSql('ALTER TABLE log_entries DROP FOREIGN KEY FK_15358B52A76ED395');
        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD68A76ED395');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F2195A76ED395');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02CA76ED395');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA03420557EC614');
        $this->addSql('ALTER TABLE client_goal DROP FOREIGN KEY FK_2D40284C33F0D3B4');
        $this->addSql('ALTER TABLE client_request DROP FOREIGN KEY FK_69AACBE233F0D3B4');
        $this->addSql('ALTER TABLE client_inventory_assignment DROP FOREIGN KEY FK_E389D12233F0D3B4');
        $this->addSql('ALTER TABLE dependent DROP FOREIGN KEY FK_BB9077A433F0D3B4');
        $this->addSql('ALTER TABLE covid_answer DROP FOREIGN KEY FK_68104D033F0D3B4');
        $this->addSql('ALTER TABLE client_goal_task DROP FOREIGN KEY FK_A76E4E8834405E83');
        $this->addSql('ALTER TABLE client_goal DROP FOREIGN KEY FK_2D40284C667D1AFE');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25667D1AFE');
        $this->addSql('ALTER TABLE newsfeed_file DROP FOREIGN KEY FK_6BF63B76641A0E5');
        $this->addSql('ALTER TABLE covid_answer DROP FOREIGN KEY FK_68104D0AD135B5B');
        $this->addSql('ALTER TABLE first_responder_detail DROP FOREIGN KEY FK_E6FC46B110E4374E');
        $this->addSql('ALTER TABLE client_goal_task DROP FOREIGN KEY FK_A76E4E888DB60186');
        $this->addSql('ALTER TABLE facility_waitlist DROP FOREIGN KEY FK_FE5086A7A7014910');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA03420A7014910');
        $this->addSql('ALTER TABLE facility_inventory DROP FOREIGN KEY FK_CC7CD73EA7014910');
        $this->addSql('ALTER TABLE facility_inventory_type DROP FOREIGN KEY FK_4ECDA983A7014910');
        $this->addSql('ALTER TABLE advocate_detail DROP FOREIGN KEY FK_71BE29E1AC8DE0F');
        $this->addSql('ALTER TABLE client_inventory_assignment DROP FOREIGN KEY FK_E389D122DF7E63A9');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA0342048FF83CD');
        $this->addSql('ALTER TABLE advocate_detail_language DROP FOREIGN KEY FK_815B3299A6FE7EB6');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA0342042CD0C4A');
        $this->addSql('ALTER TABLE client_request DROP FOREIGN KEY FK_69AACBE2427EB8A5');
        $this->addSql('ALTER TABLE client_request_comment DROP FOREIGN KEY FK_C3767DC8D22A85E4');
        $this->addSql('ALTER TABLE facility_inventory DROP FOREIGN KEY FK_CC7CD73E84482A6F');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA0342080D7D0B2');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA034204DCC3C70');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA034209771C8EE');
        $this->addSql('ALTER TABLE organization_client_type DROP FOREIGN KEY FK_6D1E30309771C8EE');
        $this->addSql('ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637C89E04D0');
        $this->addSql('ALTER TABLE advocate_detail DROP FOREIGN KEY FK_71BE29E132C8A3DE');
        $this->addSql('ALTER TABLE first_responder_detail DROP FOREIGN KEY FK_E6FC46B132C8A3DE');
        $this->addSql('ALTER TABLE organization_client_type DROP FOREIGN KEY FK_6D1E303032C8A3DE');
        $this->addSql('ALTER TABLE facility DROP FOREIGN KEY FK_105994B2753120A6');
        $this->addSql('ALTER TABLE advocate_detail_language DROP FOREIGN KEY FK_815B329982F1BAF4');
        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD6819EB6921');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F219519EB6921');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02C19EB6921');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE facility_waitlist');
        $this->addSql('DROP TABLE client_occupation');
        $this->addSql('DROP TABLE client_detail');
        $this->addSql('DROP TABLE client_goal');
        $this->addSql('DROP TABLE client_goal_task');
        $this->addSql('DROP TABLE goal');
        $this->addSql('DROP TABLE newsfeed');
        $this->addSql('DROP TABLE reset_token');
        $this->addSql('DROP TABLE covid_question');
        $this->addSql('DROP TABLE first_responder_type');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE facility');
        $this->addSql('DROP TABLE advocate_service_type');
        $this->addSql('DROP TABLE facility_inventory');
        $this->addSql('DROP TABLE advocate_detail');
        $this->addSql('DROP TABLE advocate_detail_language');
        $this->addSql('DROP TABLE first_responder_detail');
        $this->addSql('DROP TABLE user_address');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP TABLE client_request');
        $this->addSql('DROP TABLE facility_inventory_type');
        $this->addSql('DROP TABLE client_status');
        $this->addSql('DROP TABLE client_inventory_assignment');
        $this->addSql('DROP TABLE newsfeed_file');
        $this->addSql('DROP TABLE client_employment_status');
        $this->addSql('DROP TABLE client_type');
        $this->addSql('DROP TABLE organization_type');
        $this->addSql('DROP TABLE dependent');
        $this->addSql('DROP TABLE covid_answer');
        $this->addSql('DROP TABLE client_request_comment');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE organization_client_type');
        $this->addSql('DROP TABLE facility_type');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE log_entries');
        $this->addSql('DROP TABLE access_token');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE auth_code');
    }
}
