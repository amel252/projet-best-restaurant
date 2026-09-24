<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260727152959 extends AbstractMigration
{
    public function getDescription(): string
    {
       return 'Synchronisation de la table user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        // $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
         $this->addSql('ALTER TABLE user CHANGE roles roles VARCHAR(255) NOT NULL');
        // $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        // $this->addSql('ALTER TABLE user DROP profile_image, CHANGE roles roles VARCHAR(255) NOT NULL');
    }
}
