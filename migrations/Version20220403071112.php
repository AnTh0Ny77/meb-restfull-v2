<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220403071112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE qr_code (id INT AUTO_INCREMENT NOT NULL, id_client_id INT DEFAULT NULL, id_game_id INT NOT NULL, secret VARCHAR(255) NOT NULL, qr_lock TINYINT(1) NOT NULL, time INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_7D8B1FB599DED506 (id_client_id), INDEX IDX_7D8B1FB53A127075 (id_game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE qr_code ADD CONSTRAINT FK_7D8B1FB599DED506 FOREIGN KEY (id_client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE qr_code ADD CONSTRAINT FK_7D8B1FB53A127075 FOREIGN KEY (id_game_id) REFERENCES games (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE qr_code');
    }
}
