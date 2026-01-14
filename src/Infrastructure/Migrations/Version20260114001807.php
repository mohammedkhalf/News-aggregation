<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260114001807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'creation article Table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
            CREATE TABLE articles (
                id UUID NOT NULL,
                external_id VARCHAR(64) NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                content TEXT NOT NULL,
                source_name VARCHAR(255) NOT NULL,
                url VARCHAR(255) NOT NULL,
                image_url VARCHAR(255) DEFAULT NULL,
                language VARCHAR(2) NOT NULL,
                published_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )
        ');

        $this->addSql('CREATE UNIQUE INDEX uniq_external_id ON articles (external_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE articles');

    }
}
