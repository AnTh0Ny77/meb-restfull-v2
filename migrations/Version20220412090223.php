<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220412090223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE slide (id INT AUTO_INCREMENT NOT NULL, poi_id INT NOT NULL, type_slide_id INT NOT NULL, name VARCHAR(100) NOT NULL, text LONGTEXT DEFAULT NULL, text_success LONGTEXT DEFAULT NULL, text_fail LONGTEXT DEFAULT NULL, time INT DEFAULT NULL, step INT DEFAULT NULL, response VARCHAR(255) DEFAULT NULL, penality TINYINT(1) NOT NULL, cover_path VARCHAR(255) DEFAULT NULL, INDEX IDX_72EFEE627EACE855 (poi_id), INDEX IDX_72EFEE6210A5DC7B (type_slide_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_slide (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, color VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE slide ADD CONSTRAINT FK_72EFEE627EACE855 FOREIGN KEY (poi_id) REFERENCES poi (id)');
        $this->addSql('ALTER TABLE slide ADD CONSTRAINT FK_72EFEE6210A5DC7B FOREIGN KEY (type_slide_id) REFERENCES type_slide (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slide DROP FOREIGN KEY FK_72EFEE6210A5DC7B');
        $this->addSql('DROP TABLE slide');
        $this->addSql('DROP TABLE type_slide');
    }
}
