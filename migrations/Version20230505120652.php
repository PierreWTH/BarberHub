<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230505120652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE barbershop_pics DROP FOREIGN KEY FK_4C1DA855898B7F2A');
        $this->addSql('ALTER TABLE barbershop_pics ADD CONSTRAINT FK_4C1DA855898B7F2A FOREIGN KEY (barbershop_id) REFERENCES barbershop (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE barbershop_pics DROP FOREIGN KEY FK_4C1DA855898B7F2A');
        $this->addSql('ALTER TABLE barbershop_pics ADD CONSTRAINT FK_4C1DA855898B7F2A FOREIGN KEY (barbershop_id) REFERENCES barbershop (id)');
    }
}
