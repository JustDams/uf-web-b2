<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200603193724 extends AbstractMigration
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
        $this->addSql('ALTER TABLE code DROP INDEX UNIQ_7715309879F37AE5, ADD INDEX IDX_7715309879F37AE5 (id_user_id)');
        $this->addSql('ALTER TABLE code CHANGE id_user_id id_user_id INT DEFAULT NULL, CHANGE id_game_id id_game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE roles roles JSON NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cart CHANGE id_user_id id_user_id INT DEFAULT NULL, CHANGE id_game_id id_game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE code DROP INDEX IDX_7715309879F37AE5, ADD UNIQUE INDEX UNIQ_7715309879F37AE5 (id_user_id)');
        $this->addSql('ALTER TABLE code CHANGE id_game_id id_game_id INT DEFAULT NULL, CHANGE id_user_id id_user_id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_1483A5E9E7927C74 ON users');
        $this->addSql('ALTER TABLE users CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
