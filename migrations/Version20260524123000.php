<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260524123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add profile fields to Fateh user table.';
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('fateh') && $schema->getTable('fateh')->hasColumn('first_name')) {
            return;
        }

        $this->addSql('ALTER TABLE fateh ADD first_name VARCHAR(100) DEFAULT NULL, ADD last_name VARCHAR(100) DEFAULT NULL, ADD title VARCHAR(50) DEFAULT NULL, ADD address LONGTEXT DEFAULT NULL, ADD interests LONGTEXT DEFAULT NULL, ADD meal_preference VARCHAR(50) DEFAULT NULL, ADD newsletter_consent TINYINT(1) DEFAULT 0 NOT NULL, ADD data_protection_consent TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable('fateh') || !$schema->getTable('fateh')->hasColumn('first_name')) {
            return;
        }

        $this->addSql('ALTER TABLE fateh DROP first_name, DROP last_name, DROP title, DROP address, DROP interests, DROP meal_preference, DROP newsletter_consent, DROP data_protection_consent');
    }
}
