<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230525204425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prestation (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(70) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prix_prestation (id INT AUTO_INCREMENT NOT NULL, prestation_id INT DEFAULT NULL, barbershop_id INT NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_28AA47269E45C554 (prestation_id), INDEX IDX_28AA4726898B7F2A (barbershop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prix_prestation ADD CONSTRAINT FK_28AA47269E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id)');
        $this->addSql('ALTER TABLE prix_prestation ADD CONSTRAINT FK_28AA4726898B7F2A FOREIGN KEY (barbershop_id) REFERENCES barbershop (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prix_prestation DROP FOREIGN KEY FK_28AA47269E45C554');
        $this->addSql('ALTER TABLE prix_prestation DROP FOREIGN KEY FK_28AA4726898B7F2A');
        $this->addSql('DROP TABLE prestation');
        $this->addSql('DROP TABLE prix_prestation');
    }
}
