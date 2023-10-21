<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231021135338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personnel_token ADD barbershop_id INT NOT NULL');
        $this->addSql('ALTER TABLE personnel_token ADD CONSTRAINT FK_78FECA85898B7F2A FOREIGN KEY (barbershop_id) REFERENCES barbershop (id)');
        $this->addSql('CREATE INDEX IDX_78FECA85898B7F2A ON personnel_token (barbershop_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personnel_token DROP FOREIGN KEY FK_78FECA85898B7F2A');
        $this->addSql('DROP INDEX IDX_78FECA85898B7F2A ON personnel_token');
        $this->addSql('ALTER TABLE personnel_token DROP barbershop_id');
    }
}
