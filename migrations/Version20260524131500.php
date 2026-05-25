<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260524131500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create summit registration booking table.';
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('registration')) {
            return;
        }

        $this->addSql('CREATE TABLE registration (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, summit_location_id INT NOT NULL, meal_preference VARCHAR(50) DEFAULT NULL, special_needs LONGTEXT DEFAULT NULL, status VARCHAR(30) DEFAULT \'active\' NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_62A8A7A7A76ED395 (user_id), INDEX IDX_62A8A7A7F9C71096 (summit_location_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7A76ED395 FOREIGN KEY (user_id) REFERENCES taha (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7F9C71096 FOREIGN KEY (summit_location_id) REFERENCES summit_location (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable('registration')) {
            return;
        }

        $this->addSql('DROP TABLE registration');
    }
}
