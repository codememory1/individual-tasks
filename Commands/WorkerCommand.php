<?php
declare(ticks=1);

namespace Codememory\Components\IndividualTasks\Commands;

use Codememory\Components\Database\Orm\Commands\AbstractCommand;
use Codememory\Components\Database\Pack\DatabasePack;
use Codememory\Components\IndividualTasks\Worker;
use Symfony\Component\Console\Input\InputInterface;
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
    protected ?string $description = 'Start a daemon to track all tasks';

    /**
     * @inheritDoc
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $worker = new Worker(new DatabasePack($this->connection));

        $worker->daemon($this->io);

        return self::SUCCESS;

    }

}