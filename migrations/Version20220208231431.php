<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220208231431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE channel DROP authorization_type');
        $this->addSql('ALTER TABLE token ADD channel_id INT NOT NULL');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13B72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)');
        $this->addSql('CREATE INDEX IDX_5F37A13B72F5A1AA ON token (channel_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE channel ADD authorization_type VARCHAR(30) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE authorization authorization LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE label label VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE send_url send_url VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE sender_number sender_number VARCHAR(30) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE sender_name sender_name VARCHAR(30) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE received_url received_url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE sent_url sent_url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE delivered_url delivered_url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE failed_url failed_url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE stopped_url stopped_url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE get_token_auth_type get_token_auth_type VARCHAR(30) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE client_id client_id VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE client_secret client_secret VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE get_token_base_url get_token_base_url VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE delivery_notifications CHANGE delivery_callback_uuid delivery_callback_uuid VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE delivery_address delivery_address VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE delivery_status delivery_status VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE message CHANGE status status VARCHAR(25) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE send_to send_to VARCHAR(30) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE message message LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE delivery_callback_uuid delivery_callback_uuid VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE message_id message_id VARCHAR(30) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13B72F5A1AA');
        $this->addSql('DROP INDEX IDX_5F37A13B72F5A1AA ON token');
        $this->addSql('ALTER TABLE token DROP channel_id, CHANGE type type VARCHAR(30) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE access_token access_token VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
