<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210531150432 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cronoclient (cronoclient_id INT AUTO_INCREMENT NOT NULL, cronoclient_user INT NOT NULL, cronoclient_name VARCHAR(128) NOT NULL, cronoclient_color VARCHAR(16) NOT NULL, cronoclient_created_at DATETIME NOT NULL, cronoclient_modified_at DATETIME DEFAULT NULL, INDEX IDX_1DCE717611FAA392 (cronoclient_user), PRIMARY KEY(cronoclient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cronomonth (cronomonth_id INT AUTO_INCREMENT NOT NULL, cronomonth_user INT NOT NULL, cronomonth_month INT NOT NULL, cronomonth_year INT NOT NULL, cronomonth_created_at DATETIME NOT NULL, cronomonth_modified_at DATETIME DEFAULT NULL, INDEX IDX_647BE36D2CEBD26A (cronomonth_user), PRIMARY KEY(cronomonth_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cronoplan (cronoplan_id INT AUTO_INCREMENT NOT NULL, cronoplan_month INT NOT NULL, cronoplan_client INT NOT NULL, cronoplan_expected INT NOT NULL, cronoplan_created_at DATETIME NOT NULL, cronoplan_modified_at DATETIME DEFAULT NULL, INDEX IDX_C5EE09F99AC7B49A (cronoplan_month), INDEX IDX_C5EE09F93EE9AA9E (cronoplan_client), PRIMARY KEY(cronoplan_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cronotime (cronotime_id INT AUTO_INCREMENT NOT NULL, cronotime_month INT NOT NULL, cronotime_client INT NOT NULL, cronotime_start_at DATETIME NOT NULL, cronotime_end_at DATETIME NOT NULL, cronotime_title VARCHAR(128) NOT NULL, cronotime_description LONGTEXT DEFAULT NULL, cronotime_created_at DATETIME NOT NULL, cronotime_modified_at DATETIME DEFAULT NULL, INDEX IDX_7720CAC18C13101A (cronotime_month), INDEX IDX_7720CAC1D347FD1A (cronotime_client), PRIMARY KEY(cronotime_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cronoclient ADD CONSTRAINT FK_1DCE717611FAA392 FOREIGN KEY (cronoclient_user) REFERENCES user (user_id)');
        $this->addSql('ALTER TABLE cronomonth ADD CONSTRAINT FK_647BE36D2CEBD26A FOREIGN KEY (cronomonth_user) REFERENCES user (user_id)');
        $this->addSql('ALTER TABLE cronoplan ADD CONSTRAINT FK_C5EE09F99AC7B49A FOREIGN KEY (cronoplan_month) REFERENCES cronomonth (cronomonth_id)');
        $this->addSql('ALTER TABLE cronoplan ADD CONSTRAINT FK_C5EE09F93EE9AA9E FOREIGN KEY (cronoplan_client) REFERENCES cronoclient (cronoclient_id)');
        $this->addSql('ALTER TABLE cronotime ADD CONSTRAINT FK_7720CAC18C13101A FOREIGN KEY (cronotime_month) REFERENCES cronomonth (cronomonth_id)');
        $this->addSql('ALTER TABLE cronotime ADD CONSTRAINT FK_7720CAC1D347FD1A FOREIGN KEY (cronotime_client) REFERENCES cronoclient (cronoclient_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cronoplan DROP FOREIGN KEY FK_C5EE09F93EE9AA9E');
        $this->addSql('ALTER TABLE cronotime DROP FOREIGN KEY FK_7720CAC1D347FD1A');
        $this->addSql('ALTER TABLE cronoplan DROP FOREIGN KEY FK_C5EE09F99AC7B49A');
        $this->addSql('ALTER TABLE cronotime DROP FOREIGN KEY FK_7720CAC18C13101A');
        $this->addSql('DROP TABLE cronoclient');
        $this->addSql('DROP TABLE cronomonth');
        $this->addSql('DROP TABLE cronoplan');
        $this->addSql('DROP TABLE cronotime');
    }
}
