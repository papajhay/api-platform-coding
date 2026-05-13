<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260513110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add nullable image_name column to post table for VichUploaderBundle';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post ADD image_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post DROP image_name');
    }
}
