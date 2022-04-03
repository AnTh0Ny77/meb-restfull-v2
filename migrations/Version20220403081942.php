<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220403081942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE qr_code DROP FOREIGN KEY FK_7D8B1FB5D4C6B5B6');
        $this->addSql('DROP INDEX IDX_7D8B1FB5D4C6B5B6 ON qr_code');
        $this->addSql('ALTER TABLE qr_code DROP unlock_games_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE qr_code ADD unlock_games_id INT NOT NULL');
        $this->addSql('ALTER TABLE qr_code ADD CONSTRAINT FK_7D8B1FB5D4C6B5B6 FOREIGN KEY (unlock_games_id) REFERENCES unlock_games (id)');
        $this->addSql('CREATE INDEX IDX_7D8B1FB5D4C6B5B6 ON qr_code (unlock_games_id)');
    }
}
