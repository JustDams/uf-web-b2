<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200528134039 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE code (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, id_game_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, used TINYINT(1) NOT NULL, price DOUBLE PRECISION NOT NULL, purchase_date DATE NOT NULL, UNIQUE INDEX UNIQ_7715309879F37AE5 (id_user_id), INDEX IDX_771530983A127075 (id_game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, id_game_id INT NOT NULL, id_user_id INT NOT NULL, title VARCHAR(150) NOT NULL, content LONGTEXT NOT NULL, note DOUBLE PRECISION NOT NULL, INDEX IDX_5F9E962A3A127075 (id_game_id), INDEX IDX_5F9E962A79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, genres VARCHAR(255) NOT NULL, publishers VARCHAR(80) NOT NULL, review_score INT NOT NULL, price DOUBLE PRECISION NOT NULL, console VARCHAR(150) NOT NULL, release_year INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, role INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, id_role_id INT DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, birthday DATE NOT NULL, balance INT NOT NULL, register_date DATE NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E989E8BDC (id_role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE code ADD CONSTRAINT FK_7715309879F37AE5 FOREIGN KEY (id_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE code ADD CONSTRAINT FK_771530983A127075 FOREIGN KEY (id_game_id) REFERENCES games (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A3A127075 FOREIGN KEY (id_game_id) REFERENCES games (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A79F37AE5 FOREIGN KEY (id_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E989E8BDC FOREIGN KEY (id_role_id) REFERENCES roles (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE code DROP FOREIGN KEY FK_771530983A127075');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A3A127075');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E989E8BDC');
        $this->addSql('ALTER TABLE code DROP FOREIGN KEY FK_7715309879F37AE5');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A79F37AE5');
        $this->addSql('DROP TABLE code');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE users');
    }
}
