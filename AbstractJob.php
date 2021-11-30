<?php

namespace Codememory\Components\IndividualTasks;

use Codememory\Components\Database\Pack\DatabasePack;
use Codememory\Components\Database\Pack\Workers\ConnectionWorker;
use Codememory\Components\IndividualTasks\Interfaces\JobInterface;
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
     * @param DatabasePack $databasePack
     */
    public function __construct(DatabasePack $databasePack)
    {

        $this->connectionWorker = $databasePack->getConnectionWorker();
        $this->pdo = $this->connectionWorker->getConnector()->getConnection();
        $this->utils = new Utils();

    }

    /**
     * @param array $parameters
     */
    public function dispatch(array $parameters = []): void
    {

        $this->pdo
            ->prepare(sprintf('INSERT INTO `%s` (`name`, `uuid`, `payload`) VALUES (:name, :uuid, :payload)', $this->utils->getTableWithTasks()))
            ->execute([
                'name'    => static::class,
                'uuid'    => Uuid::uuid4()->toString(),
                'payload' => json_encode($parameters)
            ]);

    }

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    abstract public function handler(array $parameters = []): mixed;

}