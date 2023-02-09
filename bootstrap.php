<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\ConsoleRunner;
use MyProject\Components\Commands\RollbackCommand;

require_once "vendor/autoload.php";

try {
    $connection = DriverManager::getConnection([
        'dbname' => 'test',
        'user' => 'root',
        'password' => 'Root2839#',
        'host' => 'localhost',
        'port' => '3306',
        'driver' => 'pdo_mysql',
    ]);

    ConsoleRunner::run([new ExecuteCommand(), new GenerateCommand(), new RollbackCommand()]);
} catch (Exception $e) {
    echo $e;
}

