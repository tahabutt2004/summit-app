<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260524124500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create profile change log fields and relation to Fateh.';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('profile_change_log')) {
            $this->addSql('CREATE TABLE profile_change_log (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, field_name VARCHAR(100) NOT NULL, old_value LONGTEXT DEFAULT NULL, new_value LONGTEXT DEFAULT NULL, changed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_88B26A67A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
            $this->addSql('ALTER TABLE profile_change_log ADD CONSTRAINT FK_88B26A67A76ED395 FOREIGN KEY (user_id) REFERENCES fateh (id) ON DELETE CASCADE');

            return;
        }

        $table = $schema->getTable('profile_change_log');
        if (!$table->hasColumn('user_id')) {
            $this->addSql('ALTER TABLE profile_change_log ADD user_id INT NOT NULL');
            $this->addSql('CREATE INDEX IDX_88B26A67A76ED395 ON profile_change_log (user_id)');
            $this->addSql('ALTER TABLE profile_change_log ADD CONSTRAINT FK_88B26A67A76ED395 FOREIGN KEY (user_id) REFERENCES fateh (id) ON DELETE CASCADE');
        }
        if (!$table->hasColumn('field_name')) {
            $this->addSql('ALTER TABLE profile_change_log ADD field_name VARCHAR(100) NOT NULL');
        }
        if (!$table->hasColumn('old_value')) {
            $this->addSql('ALTER TABLE profile_change_log ADD old_value LONGTEXT DEFAULT NULL');
        }
        if (!$table->hasColumn('new_value')) {
            $this->addSql('ALTER TABLE profile_change_log ADD new_value LONGTEXT DEFAULT NULL');
        }
        if (!$table->hasColumn('changed_at')) {
            $this->addSql('ALTER TABLE profile_change_log ADD changed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        }
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable('profile_change_log')) {
            return;
        }

        $this->addSql('DROP TABLE profile_change_log');
    }
}
