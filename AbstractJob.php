<?php

namespace Codememory\Components\IndividualTasks;

use Codememory\Components\Database\Pack\DatabasePack;
use Codememory\Components\Database\Pack\Workers\ConnectionWorker;
use Codememory\Components\IndividualTasks\Interfaces\JobInterface;
use Codememory\Container\ServiceProvider\Interfaces\ServiceProviderInterface;
use PDO;
use Ramsey\Uuid\Uuid;

/**
 * Class AbstractJob
 *
 * @package Codememory\Components\IndividualTasks
 *
 * @author  Codememory
 */
abstract class AbstractJob implements JobInterface
{

    /**
     * @var ConnectionWorker
     */
    protected ConnectionWorker $connectionWorker;

    /**
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * @var Utils
     */
    protected Utils $utils;

    /**
     * @var ServiceProviderInterface
     */
    private ServiceProviderInterface $serviceProvider;

    /**
     * @param DatabasePack             $databasePack
     * @param ServiceProviderInterface $serviceProvider
     */
    public function __construct(DatabasePack $databasePack, ServiceProviderInterface $serviceProvider)
    {

        $this->connectionWorker = $databasePack->getConnectionWorker();
        $this->pdo = $this->connectionWorker->getConnector()->getConnection();
        $this->utils = new Utils();
        $this->serviceProvider = $serviceProvider;

    }

    /**
     * @param array $parameters
     */
    public function dispatch(array $parameters = []): void
    {

        $this->pdo
            ->prepare(sprintf('INSERT INTO `%s` (`name`, `uuid`, `payload`) VALUES (:name, :uuid, :payload)', $this->utils->getTableWithTasks()))
            ->execute([
                'name'      => static::class,
                'uuid'      => Uuid::uuid4()->toString(),
                'payload'   => json_encode($parameters)
            ]);

    }

    /**
     * @param string $name
     *
     * @return object
     */
    protected function get(string $name): object
    {

        return $this->serviceProvider->get($name);

    }

}