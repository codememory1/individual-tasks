<?php
declare(ticks=1);

namespace Codememory\Components\IndividualTasks\Commands;

use Codememory\Components\Database\Connection\Interfaces\ConnectionInterface;
use Codememory\Components\Database\Connection\Interfaces\ConnectorInterface;
use Codememory\Components\Database\Orm\Commands\AbstractCommand;
use Codememory\Components\Database\Pack\DatabasePack;
use Codememory\Components\IndividualTasks\Worker;
use Codememory\Container\ServiceProvider\Interfaces\ServiceProviderInterface;
use Exception;
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
     * @var ServiceProviderInterface
     */
    private ServiceProviderInterface $serviceProvider;

    /**
     * @param ConnectorInterface       $connector
     * @param ConnectionInterface      $connection
     * @param ServiceProviderInterface $serviceProvider
     *
     * @throws Exception
     */
    public function __construct(ConnectorInterface $connector, ConnectionInterface $connection, ServiceProviderInterface $serviceProvider)
    {

        parent::__construct($connector, $connection);

        $this->serviceProvider = $serviceProvider;

    }

    /**
     * @inheritDoc
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $worker = new Worker(new DatabasePack($this->connection), $this->serviceProvider);

        $worker->daemon($this->io);

        return self::SUCCESS;

    }

}