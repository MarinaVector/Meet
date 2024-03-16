<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312163808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE meet (id BIGSERIAL NOT NULL, author_id BIGINT DEFAULT NULL, text VARCHAR(250) NOT NULL, format VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX meet__author_id__ind ON meet (author_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_smth ON meet (author_id, text)');
        $this->addSql('CREATE TABLE subscription (id BIGSERIAL NOT NULL, author_id BIGINT DEFAULT NULL, follower_id BIGINT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX subscription__author_id__ind ON subscription (author_id)');
        $this->addSql('CREATE INDEX subscription__follower_id__ind ON subscription (follower_id)');
        $this->addSql('CREATE TABLE "user" (id BIGSERIAL NOT NULL, login VARCHAR(32) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, password VARCHAR(32) NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE author_follower (author_id BIGINT NOT NULL, follower_id BIGINT NOT NULL, PRIMARY KEY(author_id, follower_id))');
        $this->addSql('CREATE INDEX IDX_564623F3F675F31B ON author_follower (author_id)');
        $this->addSql('CREATE INDEX IDX_564623F3AC24F853 ON author_follower (follower_id)');
        $this->addSql('ALTER TABLE meet ADD CONSTRAINT FK_E9F6D3CEF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3AC24F853 FOREIGN KEY (follower_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE author_follower ADD CONSTRAINT FK_564623F3F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE author_follower ADD CONSTRAINT FK_564623F3AC24F853 FOREIGN KEY (follower_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meet DROP CONSTRAINT FK_E9F6D3CEF675F31B');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3F675F31B');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3AC24F853');
        $this->addSql('ALTER TABLE author_follower DROP CONSTRAINT FK_564623F3F675F31B');
        $this->addSql('ALTER TABLE author_follower DROP CONSTRAINT FK_564623F3AC24F853');
        $this->addSql('DROP TABLE meet');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE author_follower');
    }
}