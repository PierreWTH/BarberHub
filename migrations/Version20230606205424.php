<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606205424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE barber_prestation (id INT AUTO_INCREMENT NOT NULL, barbershop_id INT NOT NULL, prestation_id INT NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_B8DFB2C4898B7F2A (barbershop_id), INDEX IDX_B8DFB2C49E45C554 (prestation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE barber_prestation ADD CONSTRAINT FK_B8DFB2C4898B7F2A FOREIGN KEY (barbershop_id) REFERENCES barbershop (id)');
        $this->addSql('ALTER TABLE barber_prestation ADD CONSTRAINT FK_B8DFB2C49E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE barber_prestation DROP FOREIGN KEY FK_B8DFB2C4898B7F2A');
        $this->addSql('ALTER TABLE barber_prestation DROP FOREIGN KEY FK_B8DFB2C49E45C554');
        $this->addSql('DROP TABLE barber_prestation');
    }
}
