<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200320063019 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE facility_inventory ADD facility_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE facility_inventory ADD CONSTRAINT FK_CC7CD73EA7014910 FOREIGN KEY (facility_id) REFERENCES facility (id)');
        $this->addSql('CREATE INDEX IDX_CC7CD73EA7014910 ON facility_inventory (facility_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE facility_inventory DROP FOREIGN KEY FK_CC7CD73EA7014910');
        $this->addSql('DROP INDEX IDX_CC7CD73EA7014910 ON facility_inventory');
        $this->addSql('ALTER TABLE facility_inventory DROP facility_id');
    }
}
