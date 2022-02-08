<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220208165118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE token (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(30) NOT NULL, access_token VARCHAR(255) NOT NULL, expire_date DATETIME NOT NULL, create_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE channel ADD authorization_type VARCHAR(30) NOT NULL, ADD get_token_auth_type VARCHAR(30) NOT NULL, ADD client_id VARCHAR(255) DEFAULT NULL, ADD client_secret VARCHAR(255) DEFAULT NULL, ADD get_token_base_url VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE token');
        $this->addSql('ALTER TABLE channel DROP authorization_type, DROP get_token_auth_type, DROP client_id, DROP client_secret, DROP get_token_base_url, CHANGE authorization authorization LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE label label VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE send_url send_url VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE sender_number sender_number VARCHAR(30) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE sender_name sender_name VARCHAR(30) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE received_url received_url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE sent_url sent_url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE delivered_url delivered_url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE failed_url failed_url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE stopped_url stopped_url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE delivery_notifications CHANGE delivery_callback_uuid delivery_callback_uuid VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE delivery_address delivery_address VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE delivery_status delivery_status VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE message CHANGE status status VARCHAR(25) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE send_to send_to VARCHAR(30) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE message message LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE delivery_callback_uuid delivery_callback_uuid VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE message_id message_id VARCHAR(30) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
