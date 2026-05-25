<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260524130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create summit location table.';
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('summit_location')) {
            return;
        }

        $this->addSql('CREATE TABLE summit_location (id INT AUTO_INCREMENT NOT NULL, city VARCHAR(100) NOT NULL, capacity INT NOT NULL, event_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', location_name VARCHAR(150) NOT NULL, address LONGTEXT NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable('summit_location')) {
            return;
        }

        $this->addSql('DROP TABLE summit_location');
    }
}
