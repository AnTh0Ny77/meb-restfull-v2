<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220525120917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quest_score (id INT AUTO_INCREMENT NOT NULL, quest_id_id INT NOT NULL, user_id_id INT NOT NULL, score INT NOT NULL, INDEX IDX_6124AECD2CF907CB (quest_id_id), INDEX IDX_6124AECD9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quest_score ADD CONSTRAINT FK_6124AECD2CF907CB FOREIGN KEY (quest_id_id) REFERENCES quest (id)');
        $this->addSql('ALTER TABLE quest_score ADD CONSTRAINT FK_6124AECD9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE quest_score');
    }
}
