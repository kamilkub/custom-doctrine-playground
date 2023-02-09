<?php

declare(strict_types=1);

namespace MyProject\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230209191255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO `player` (`name`) VALUES ('Jason');");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM `player` WHERE `name` LIKE 'Jason';");
    }
}
