<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230505064338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE barbershop_pics (id INT AUTO_INCREMENT NOT NULL, barbershop_id INT NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_4C1DA855898B7F2A (barbershop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE barbershop_pics ADD CONSTRAINT FK_4C1DA855898B7F2A FOREIGN KEY (barbershop_id) REFERENCES barbershop (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE barbershop_pics DROP FOREIGN KEY FK_4C1DA855898B7F2A');
        $this->addSql('DROP TABLE barbershop_pics');
    }
}
