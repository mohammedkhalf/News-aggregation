<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fix external_id column length to support long URLs (was VARCHAR(64), now VARCHAR(255))
 */
final class Version20260117024915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Alter external_id column to VARCHAR(255) to support long URLs';
    }

    public function up(Schema $schema): void
    {
        // Alter external_id column to support longer URLs
        $this->addSql('ALTER TABLE articles ALTER COLUMN external_id TYPE VARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        // Revert back to VARCHAR(64)
        $this->addSql('ALTER TABLE articles ALTER COLUMN external_id TYPE VARCHAR(64)');
    }
}
