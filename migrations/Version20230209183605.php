<?php

declare(strict_types=1);

namespace MyProject\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230209183605 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            "CREATE TABLE `player` (
                            `id` INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
                            `name` VARCHAR(255) NOT NULL
                        );"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `player`;');
    }
}
