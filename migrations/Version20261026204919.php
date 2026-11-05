<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261026204919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE site_information (id INT AUTO_INCREMENT NOT NULL, site_name VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, address VARCHAR(255) NOT NULL, phone VARCHAR(30) NOT NULL, email VARCHAR(180) NOT NULL, twitter_url VARCHAR(255) NOT NULL, facebook_url VARCHAR(255) NOT NULL, instagram_url VARCHAR(255) NOT NULL, linkedin_url VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE site_information');
    }
}
