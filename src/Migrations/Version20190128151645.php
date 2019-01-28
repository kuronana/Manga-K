<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190128151645 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE episodes (id INT AUTO_INCREMENT NOT NULL, anime_id INT NOT NULL, season INT NOT NULL, episodes VARCHAR(255) DEFAULT NULL, INDEX IDX_7DD55EDD794BBE89 (anime_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, anime_id INT DEFAULT NULL, user_id INT DEFAULT NULL, date_time DATETIME DEFAULT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_9474526C794BBE89 (anime_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE animes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, synopsis LONGTEXT NOT NULL, duration INT NOT NULL, image VARCHAR(255) NOT NULL, image_card VARCHAR(255) DEFAULT NULL, image_card_blur VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE animes_type (animes_id INT NOT NULL, type_id INT NOT NULL, INDEX IDX_CD3C8240736CCD1F (animes_id), INDEX IDX_CD3C8240C54C8C93 (type_id), PRIMARY KEY(animes_id, type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, roles VARCHAR(255) NOT NULL, picture VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D6495126AC48 (mail), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE episodes ADD CONSTRAINT FK_7DD55EDD794BBE89 FOREIGN KEY (anime_id) REFERENCES animes (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C794BBE89 FOREIGN KEY (anime_id) REFERENCES animes (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE animes_type ADD CONSTRAINT FK_CD3C8240736CCD1F FOREIGN KEY (animes_id) REFERENCES animes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animes_type ADD CONSTRAINT FK_CD3C8240C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE episodes DROP FOREIGN KEY FK_7DD55EDD794BBE89');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C794BBE89');
        $this->addSql('ALTER TABLE animes_type DROP FOREIGN KEY FK_CD3C8240736CCD1F');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE animes_type DROP FOREIGN KEY FK_CD3C8240C54C8C93');
        $this->addSql('DROP TABLE episodes');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE animes');
        $this->addSql('DROP TABLE animes_type');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE type');
    }
}
