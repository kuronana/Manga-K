<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190114053016 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE animes_type (animes_id INT NOT NULL, type_id INT NOT NULL, INDEX IDX_CD3C8240736CCD1F (animes_id), INDEX IDX_CD3C8240C54C8C93 (type_id), PRIMARY KEY(animes_id, type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animes_type ADD CONSTRAINT FK_CD3C8240736CCD1F FOREIGN KEY (animes_id) REFERENCES animes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animes_type ADD CONSTRAINT FK_CD3C8240C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE animes_type DROP FOREIGN KEY FK_CD3C8240C54C8C93');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE animes_type');
    }
}
