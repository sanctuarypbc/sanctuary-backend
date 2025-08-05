<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200812111136 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE client_goal (id INT AUTO_INCREMENT NOT NULL, client_detail_id INT NOT NULL, goal_id INT NOT NULL, assigned_by_id INT NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_2D40284C33F0D3B4 (client_detail_id), INDEX IDX_2D40284C667D1AFE (goal_id), INDEX IDX_2D40284C6E6F1246 (assigned_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_goal_task (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, client_goal_id INT NOT NULL, completed TINYINT(1) DEFAULT \'0\' NOT NULL, completed_at DATETIME DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_A76E4E888DB60186 (task_id), INDEX IDX_A76E4E8834405E83 (client_goal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client_goal ADD CONSTRAINT FK_2D40284C33F0D3B4 FOREIGN KEY (client_detail_id) REFERENCES client_detail (id)');
        $this->addSql('ALTER TABLE client_goal ADD CONSTRAINT FK_2D40284C667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id)');
        $this->addSql('ALTER TABLE client_goal ADD CONSTRAINT FK_2D40284C6E6F1246 FOREIGN KEY (assigned_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE client_goal_task ADD CONSTRAINT FK_A76E4E888DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE client_goal_task ADD CONSTRAINT FK_A76E4E8834405E83 FOREIGN KEY (client_goal_id) REFERENCES client_goal (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client_goal_task DROP FOREIGN KEY FK_A76E4E8834405E83');
        $this->addSql('DROP TABLE client_goal');
        $this->addSql('DROP TABLE client_goal_task');
    }
}
