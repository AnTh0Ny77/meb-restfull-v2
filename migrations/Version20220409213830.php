<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220409213830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE poi (id INT AUTO_INCREMENT NOT NULL, quest_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, latlng JSON NOT NULL, text LONGTEXT DEFAULT NULL, clue VARCHAR(255) DEFAULT NULL, image_clue VARCHAR(100) DEFAULT NULL, INDEX IDX_7DBB1FD6209E9EF4 (quest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE poi ADD CONSTRAINT FK_7DBB1FD6209E9EF4 FOREIGN KEY (quest_id) REFERENCES quest (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE poi');
    }
}
