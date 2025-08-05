<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200409072442 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dependent DROP FOREIGN KEY FK_BB9077A4A76ED395');
        $this->addSql('DROP INDEX IDX_BB9077A4A76ED395 ON dependent');
        $this->addSql('ALTER TABLE dependent CHANGE user_id client_detail_id INT NOT NULL');
        $this->addSql('ALTER TABLE dependent ADD CONSTRAINT FK_BB9077A433F0D3B4 FOREIGN KEY (client_detail_id) REFERENCES client_detail (id)');
        $this->addSql('CREATE INDEX IDX_BB9077A433F0D3B4 ON dependent (client_detail_id)');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA0342042CD0C4A');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA0342048FF83CD');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA0342042CD0C4A FOREIGN KEY (first_responder_id) REFERENCES first_responder_detail (id)');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA0342048FF83CD FOREIGN KEY (advocate_id) REFERENCES advocate_detail (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA0342042CD0C4A');
        $this->addSql('ALTER TABLE client_detail DROP FOREIGN KEY FK_7BA0342048FF83CD');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA0342042CD0C4A FOREIGN KEY (first_responder_id) REFERENCES fos_user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE client_detail ADD CONSTRAINT FK_7BA0342048FF83CD FOREIGN KEY (advocate_id) REFERENCES fos_user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dependent DROP FOREIGN KEY FK_BB9077A433F0D3B4');
        $this->addSql('DROP INDEX IDX_BB9077A433F0D3B4 ON dependent');
        $this->addSql('ALTER TABLE dependent CHANGE client_detail_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE dependent ADD CONSTRAINT FK_BB9077A4A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_BB9077A4A76ED395 ON dependent (user_id)');
    }
}
