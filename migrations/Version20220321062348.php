<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220321062348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE daily_station_equipment_counters (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, station_id BIGINT UNSIGNED NOT NULL, equipment_type_id BIGINT UNSIGNED NOT NULL, count INT UNSIGNED NOT NULL, day_code VARCHAR(8) NOT NULL, dtype VARCHAR(6) NOT NULL, INDEX IDX_3B26983321BDB235 (station_id), INDEX IDX_3B269833B337437C (equipment_type_id), INDEX IDX_3B26983321BDB235DC7DDDEE (station_id, day_code), UNIQUE INDEX UNIQ_3B26983370AAEA521BDB235B337437CDC7DDDEE (dtype, station_id, equipment_type_id, day_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment_types (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, name LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_equipment_counters (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, order_id BIGINT UNSIGNED NOT NULL, equipment_type_id BIGINT UNSIGNED NOT NULL, count INT NOT NULL, INDEX IDX_897F7B818D9F6D38 (order_id), INDEX IDX_897F7B81B337437C (equipment_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, start_station_id BIGINT UNSIGNED NOT NULL, end_station_id BIGINT UNSIGNED NOT NULL, start_day_code VARCHAR(8) NOT NULL, end_day_code VARCHAR(8) NOT NULL, INDEX IDX_E52FFDEE53721DCB (start_station_id), INDEX IDX_E52FFDEE2FF5EABB (end_station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stations (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, name LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE daily_station_equipment_counters ADD CONSTRAINT FK_3B26983321BDB235 FOREIGN KEY (station_id) REFERENCES stations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE daily_station_equipment_counters ADD CONSTRAINT FK_3B269833B337437C FOREIGN KEY (equipment_type_id) REFERENCES equipment_types (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_equipment_counters ADD CONSTRAINT FK_897F7B818D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_equipment_counters ADD CONSTRAINT FK_897F7B81B337437C FOREIGN KEY (equipment_type_id) REFERENCES equipment_types (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE53721DCB FOREIGN KEY (start_station_id) REFERENCES stations (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE2FF5EABB FOREIGN KEY (end_station_id) REFERENCES stations (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE daily_station_equipment_counters DROP FOREIGN KEY FK_3B269833B337437C');
        $this->addSql('ALTER TABLE order_equipment_counters DROP FOREIGN KEY FK_897F7B81B337437C');
        $this->addSql('ALTER TABLE order_equipment_counters DROP FOREIGN KEY FK_897F7B818D9F6D38');
        $this->addSql('ALTER TABLE daily_station_equipment_counters DROP FOREIGN KEY FK_3B26983321BDB235');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE53721DCB');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE2FF5EABB');
        $this->addSql('DROP TABLE daily_station_equipment_counters');
        $this->addSql('DROP TABLE equipment_types');
        $this->addSql('DROP TABLE order_equipment_counters');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE stations');
    }
}
