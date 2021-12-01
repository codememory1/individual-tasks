<?php
declare(ticks=1);

namespace Codememory\Components\IndividualTasks\Commands;

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
     * @var DatabasePack
     */
    private DatabasePack $databasePack;

    /**
     * @param DatabasePack             $databasePack
     * @param ServiceProviderInterface $serviceProvider
     *
     * @throws Exception
     */
    public function __construct(DatabasePack $databasePack, ServiceProviderInterface $serviceProvider)
    {

        $connector = $databasePack->getConnectionWorker()->getConnector();
        $connection = $databasePack->getConnectionWorker()->getConnection();

        parent::__construct($connector, $connection);

        $this->serviceProvider = $serviceProvider;
        $this->databasePack = $databasePack;

    }

    /**
     * @inheritDoc
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $worker = new Worker($this->databasePack, $this->serviceProvider);

        $worker->daemon($this->io);

        return self::SUCCESS;

    }

}