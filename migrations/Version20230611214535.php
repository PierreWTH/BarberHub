<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230611214535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personnel DROP INDEX IDX_A6BCF3DEA76ED395, ADD UNIQUE INDEX UNIQ_A6BCF3DEA76ED395 (user_id)');
        $this->addSql('ALTER TABLE personnel CHANGE barbershop_id barbershop_id INT NOT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE is_manager manager TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE rendez_vous ADD personnel_id INT NOT NULL');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A1C109075 FOREIGN KEY (personnel_id) REFERENCES personnel (id)');
        $this->addSql('CREATE INDEX IDX_65E8AA0A1C109075 ON rendez_vous (personnel_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personnel DROP INDEX UNIQ_A6BCF3DEA76ED395, ADD INDEX IDX_A6BCF3DEA76ED395 (user_id)');
        $this->addSql('ALTER TABLE personnel CHANGE barbershop_id barbershop_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE manager is_manager TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A1C109075');
        $this->addSql('DROP INDEX IDX_65E8AA0A1C109075 ON rendez_vous');
        $this->addSql('ALTER TABLE rendez_vous DROP personnel_id');
    }
}
