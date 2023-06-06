<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606210253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rendez_vous_barber_prestation (rendez_vous_id INT NOT NULL, barber_prestation_id INT NOT NULL, INDEX IDX_A7EAE3B491EF7EAA (rendez_vous_id), INDEX IDX_A7EAE3B43078F86B (barber_prestation_id), PRIMARY KEY(rendez_vous_id, barber_prestation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rendez_vous_barber_prestation ADD CONSTRAINT FK_A7EAE3B491EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rendez_vous_barber_prestation ADD CONSTRAINT FK_A7EAE3B43078F86B FOREIGN KEY (barber_prestation_id) REFERENCES barber_prestation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendez_vous_barber_prestation DROP FOREIGN KEY FK_A7EAE3B491EF7EAA');
        $this->addSql('ALTER TABLE rendez_vous_barber_prestation DROP FOREIGN KEY FK_A7EAE3B43078F86B');
        $this->addSql('DROP TABLE rendez_vous_barber_prestation');
    }
}
