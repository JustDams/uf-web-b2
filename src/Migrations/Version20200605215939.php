<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200605215939 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cart CHANGE id_user_id id_user_id INT DEFAULT NULL, CHANGE id_game_id id_game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE code CHANGE id_user_id id_user_id INT DEFAULT NULL, CHANGE id_game_id id_game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE games CHANGE stock stock INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cart CHANGE id_user_id id_user_id INT DEFAULT NULL, CHANGE id_game_id id_game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE code CHANGE id_user_id id_user_id INT DEFAULT NULL, CHANGE id_game_id id_game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE games CHANGE stock stock INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
