<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220403082154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE unlock_games ADD qr_code_id INT NOT NULL');
        $this->addSql('ALTER TABLE unlock_games ADD CONSTRAINT FK_9E93FE7712E4AD80 FOREIGN KEY (qr_code_id) REFERENCES qr_code (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9E93FE7712E4AD80 ON unlock_games (qr_code_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE unlock_games DROP FOREIGN KEY FK_9E93FE7712E4AD80');
        $this->addSql('DROP INDEX UNIQ_9E93FE7712E4AD80 ON unlock_games');
        $this->addSql('ALTER TABLE unlock_games DROP qr_code_id');
    }
}
