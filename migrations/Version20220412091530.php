<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220412091530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bag_tools (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, color VARCHAR(50) DEFAULT NULL, cover_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bag_tools_games (bag_tools_id INT NOT NULL, games_id INT NOT NULL, INDEX IDX_6206A904A0387B5B (bag_tools_id), INDEX IDX_6206A90497FFC673 (games_id), PRIMARY KEY(bag_tools_id, games_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bag_tools_games ADD CONSTRAINT FK_6206A904A0387B5B FOREIGN KEY (bag_tools_id) REFERENCES bag_tools (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bag_tools_games ADD CONSTRAINT FK_6206A90497FFC673 FOREIGN KEY (games_id) REFERENCES games (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bag_tools_games DROP FOREIGN KEY FK_6206A904A0387B5B');
        $this->addSql('DROP TABLE bag_tools');
        $this->addSql('DROP TABLE bag_tools_games');
    }
}
