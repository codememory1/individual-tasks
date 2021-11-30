<?php
declare(ticks = 1);

namespace Codememory\Components\IndividualTasks\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\Database\Orm\Commands\AbstractCommand;
use Codememory\Components\Database\Pack\DatabasePack;
use Codememory\Components\Environment\Environment;
use Codememory\Components\IndividualTasks\Worker;
use Codememory\FileSystem\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WorkerCommand
 *
 * @package Codememory\Components\IndividualTasks\Commands
 *
 * @author  Codememory
 */
class WorkerCommand extends AbstractCommand
{

    /**
     * @inheritDoc
     */
    protected ?string $command = 'it:worker';

    /**
     * @inheritDoc
     */
    protected ?string $description = '';

    /**
     * @inheritDoc
     */
    protected function wrapArgsAndOptions(): Command
    {

        $this->addOption('failed', 'f', InputOption::VALUE_NONE, 'Restart jobs that were not executed');

        return $this;

    }

    /**
     * @inheritDoc
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        Environment::__constructStatic(new File());

        $worker = new Worker(new DatabasePack($this->connection));

        $worker->daemon($this->io);

        return self::SUCCESS;

    }

}