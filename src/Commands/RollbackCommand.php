<?php

namespace MyProject\Components\Commands;

use Doctrine\Migrations\Exception\MigrationNotExecuted;
use Doctrine\Migrations\Metadata\AvailableMigration;
use Doctrine\Migrations\Metadata\ExecutedMigration;
use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;
use Doctrine\Migrations\Version\Direction;
use Doctrine\Migrations\Version\Version;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RollbackCommand extends DoctrineCommand
{
    protected static $defaultName = 'migrations:rollback';

    protected function configure(): void
    {
        $this
            ->setAliases(['rollback'])
            ->setDescription(
                'Rollback migrations to specific version manually.'
            )
            ->addArgument(
                '-v',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Migration version to rollback to.',
                null
            )
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command executes all migration down to specified version
                        <info>%command.full_name% FQCN</info>
EOT
            );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migratorConfigurationFactory = $this->getDependencyFactory()->getConsoleInputMigratorConfigurationFactory();
        $migratorConfiguration = $migratorConfigurationFactory->getMigratorConfiguration($input);

        $rollbackVersion = $input->getArgument('-v')[0];

        $executedVersions = $this->getExecutedVersions();

        $migrationIndex = $this->getIndexOfRollbackVersion($rollbackVersion, $executedVersions);

        $this->getDependencyFactory()->getMetadataStorage()->ensureInitialized();

        $versionsForRollback = array_slice($executedVersions, $migrationIndex + 1, count($executedVersions));
        $direction = Direction::DOWN;

        $planCalculator = $this->getDependencyFactory()->getMigrationPlanCalculator();
        $plan = $planCalculator->getPlanForVersions(
            array_map(static function (string $version): Version {
                return new Version($version);
            }, $versionsForRollback),
            $direction
        );

        $this->getDependencyFactory()->getLogger()->notice(
            'Executing {versions} {direction}',
            [
                'direction' => $plan->getDirection(),
                'versions' => implode(', ', $versionsForRollback),
            ]
        );

        $migrator = $this->getDependencyFactory()->getMigrator();
        $migrator->migrate($plan, $migratorConfiguration);

        $this->io->newLine();

        return 0;
    }

    private function getIndexOfRollbackVersion(string $rollbackVersion, array $versions): int
    {
        $migrationIndex = array_search($rollbackVersion, $versions);

        if ($migrationIndex === false) {
            $this->io->error('Such migration does not exist.');
            return 1;
        }

        return $migrationIndex;
    }

    private function getExecutedVersions(): array
    {
        return array_map(function (ExecutedMigration $migration) {
            return $migration->getVersion()->__toString();
        }, $this->getDependencyFactory()->getMetadataStorage()->getExecutedMigrations()->getItems());
    }

}
