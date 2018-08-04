<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180804122302 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE edition (id INT AUTO_INCREMENT NOT NULL, annee INT NOT NULL, dates VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, game_slot_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(1024) DEFAULT NULL, seats INT NOT NULL, force_online_seats TINYINT(1) NOT NULL, INDEX IDX_232B318CF675F31B (author_id), INDEX IDX_232B318CCC276EB3 (game_slot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_user (game_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6686BA65E48FD905 (game_id), INDEX IDX_6686BA65A76ED395 (user_id), PRIMARY KEY(game_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_slot (id INT AUTO_INCREMENT NOT NULL, edition_id INT NOT NULL, day INT NOT NULL, INDEX IDX_471B4C4B74281A5E (edition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, slug VARCHAR(128) NOT NULL, date_created DATE DEFAULT CURRENT_DATE NOT NULL, INDEX IDX_1DD39950F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, first_name VARCHAR(64) NOT NULL, pseudo VARCHAR(64) NOT NULL, password VARCHAR(64) NOT NULL, email VARCHAR(254) NOT NULL, avatar VARCHAR(512) DEFAULT NULL, date_created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, is_active TINYINT(1) DEFAULT \'1\' NOT NULL, roles VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_C250282486CC499D (pseudo), UNIQUE INDEX UNIQ_C2502824E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CF675F31B FOREIGN KEY (author_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CCC276EB3 FOREIGN KEY (game_slot_id) REFERENCES game_slot (id)');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_slot ADD CONSTRAINT FK_471B4C4B74281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD39950F675F31B FOREIGN KEY (author_id) REFERENCES app_users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game_slot DROP FOREIGN KEY FK_471B4C4B74281A5E');
        $this->addSql('ALTER TABLE game_user DROP FOREIGN KEY FK_6686BA65E48FD905');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CCC276EB3');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CF675F31B');
        $this->addSql('ALTER TABLE game_user DROP FOREIGN KEY FK_6686BA65A76ED395');
        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD39950F675F31B');
        $this->addSql('DROP TABLE edition');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_user');
        $this->addSql('DROP TABLE game_slot');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE app_users');
    }
}
