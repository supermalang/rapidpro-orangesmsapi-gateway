<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210602160823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE channel ADD received_url VARCHAR(255) DEFAULT NULL, ADD sent_url VARCHAR(255) DEFAULT NULL, ADD delivered_url VARCHAR(255) DEFAULT NULL, ADD failed_url VARCHAR(255) DEFAULT NULL, ADD stopped_url VARCHAR(255) DEFAULT NULL, CHANGE send_url send_url VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE channel DROP received_url, DROP sent_url, DROP delivered_url, DROP failed_url, DROP stopped_url, CHANGE send_url send_url LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
