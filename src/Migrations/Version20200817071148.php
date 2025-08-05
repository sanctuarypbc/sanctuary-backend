<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200817071148 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE newsfeed_file (id INT AUTO_INCREMENT NOT NULL, news_feed_id INT NOT NULL, type VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_6BF63B76641A0E5 (news_feed_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsfeed (id INT AUTO_INCREMENT NOT NULL, headline LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, show_to_client TINYINT(1) NOT NULL, link LONGTEXT DEFAULT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE newsfeed_file ADD CONSTRAINT FK_6BF63B76641A0E5 FOREIGN KEY (news_feed_id) REFERENCES newsfeed (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE newsfeed_file DROP FOREIGN KEY FK_6BF63B76641A0E5');
        $this->addSql('DROP TABLE newsfeed_file');
        $this->addSql('DROP TABLE newsfeed');
    }
}
