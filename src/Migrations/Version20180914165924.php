<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180914165924 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_users CHANGE avatar avatar VARCHAR(512) DEFAULT NULL, CHANGE date_created date_created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE roles roles VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE game ADD validated TINYINT(1) NOT NULL, CHANGE game_slot_id game_slot_id INT DEFAULT NULL, CHANGE image image VARCHAR(1024) DEFAULT NULL');
        $this->addSql('ALTER TABLE news CHANGE date_created date_created DATE DEFAULT CURRENT_DATE NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_users CHANGE avatar avatar VARCHAR(512) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE date_created date_created DATETIME DEFAULT \'current_timestamp()\' NOT NULL, CHANGE roles roles VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE game DROP validated, CHANGE game_slot_id game_slot_id INT DEFAULT NULL, CHANGE image image VARCHAR(1024) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE news CHANGE date_created date_created DATE DEFAULT \'curdate()\' NOT NULL');
    }
}
