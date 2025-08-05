<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191231123911 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dependent DROP FOREIGN KEY FK_BB9077A433F0D3B4');
        $this->addSql('DROP INDEX IDX_BB9077A433F0D3B4 ON dependent');
        $this->addSql('ALTER TABLE dependent DROP client_detail_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dependent ADD client_detail_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dependent ADD CONSTRAINT FK_BB9077A433F0D3B4 FOREIGN KEY (client_detail_id) REFERENCES client_detail (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_BB9077A433F0D3B4 ON dependent (client_detail_id)');
    }
}
