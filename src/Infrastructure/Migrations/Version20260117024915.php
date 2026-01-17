<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fix many columns length to support long URLs (was VARCHAR(255), now TEXT)
 */
final class Version20260117024915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Alter many columns from VARCHAR(255) to TEXT';
    }

    public function up(Schema $schema): void
    {
        // Alter external_id column to support longer URLs
        $this->addSql('ALTER TABLE articles ALTER COLUMN external_id TYPE TEXT');
        $this->addSql('ALTER TABLE articles ALTER COLUMN title TYPE TEXT');
        $this->addSql('ALTER TABLE articles ALTER COLUMN source_name TYPE TEXT');
        $this->addSql('ALTER TABLE articles ALTER COLUMN url TYPE TEXT');
        $this->addSql('ALTER TABLE articles ALTER COLUMN image_url TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        // Revert back to VARCHAR(64)
        $this->addSql('ALTER TABLE articles ALTER COLUMN external_id TYPE TEXT');
        $this->addSql('ALTER TABLE articles ALTER COLUMN title TYPE TEXT');
        $this->addSql('ALTER TABLE articles ALTER COLUMN source_name TYPE TEXT');
        $this->addSql('ALTER TABLE articles ALTER COLUMN url TYPE TEXT');
        $this->addSql('ALTER TABLE articles ALTER COLUMN image_url TYPE TEXT');
    }
}
