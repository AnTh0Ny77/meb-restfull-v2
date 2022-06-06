<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220606174744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE poi_score (id INT AUTO_INCREMENT NOT NULL, poi_id INT NOT NULL, user_id INT NOT NULL, score INT NOT NULL, finished TINYINT(1) NOT NULL, INDEX IDX_82C16347EACE855 (poi_id), INDEX IDX_82C1634A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE poi_score ADD CONSTRAINT FK_82C16347EACE855 FOREIGN KEY (poi_id) REFERENCES poi (id)');
        $this->addSql('ALTER TABLE poi_score ADD CONSTRAINT FK_82C1634A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE poi_score');
    }
}
