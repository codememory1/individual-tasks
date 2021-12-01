<?php

namespace Codememory\Components\IndividualTasks\Commands;

use Codememory\Components\Database\Orm\Commands\AbstractCommand;
use Codememory\Components\IndividualTasks\Utils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateTablesCommand
 *
 * @package Codememory\Components\IndividualTasks\Commands
 *
 * @author  Codememory
 */
class CreateTablesCommand extends AbstractCommand
{

    /**
     * @inheritDoc
     */
    protected ?string $command = 'it:create-tables';

    /**
     * @inheritDoc
     */
    protected ?string $description = 'Create all required tables';

    /**
     * @inheritDoc
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        if (false === $this->isConnection($this->connector)) {
            $this->checkConnection();

            return self::FAILURE;
        }

        $utils = new Utils();

        $this->getConnection()
            ->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS `{$utils->getTableWithTasks()}` (
                `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `uuid` VARCHAR(36) NOT NULL,
                `payload` JSON NULL DEFAULT NULL,
                `providers` JSON NULL DEFAULT NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS `{$utils->getTableWithInfo()}` (
                `key` VARCHAR(100) NOT NULL,
                `value` TEXT NOT NULL
            );
            INSERT INTO `{$utils->getTableWithInfo()}` (`key`, `value`) VALUES ('status', 0)
            SQL
            );

        $this->io->success(sprintf('Tables: %s created successfully', implode(',', [
            $utils->getTableWithTasks(),
            $utils->getTableWithInfo()
        ])));

        return self::SUCCESS;

    }

}