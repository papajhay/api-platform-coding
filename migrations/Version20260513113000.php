<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260513113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove slug column from post table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_5A8A6C8D989D9B62 ON post');
        $this->addSql('ALTER TABLE post DROP slug');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8D989D9B62 ON post (slug)');
    }
}
