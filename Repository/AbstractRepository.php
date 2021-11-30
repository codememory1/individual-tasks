<?php

namespace Codememory\Components\IndividualTasks\Repository;

use Codememory\Components\Database\Connection\Interfaces\ConnectorInterface;
use Codememory\Components\IndividualTasks\Utils;
use PDO;

/**
 * Class AbstractRepository
 *
 * @package Codememory\Components\IndividualTasks\Repository
 *
 * @author  Codememory
 */
abstract class AbstractRepository
{

    /**
     * @var ConnectorInterface
     */
    protected ConnectorInterface $connector;

    /**
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * @var Utils
     */
    protected Utils $utils;

    /**
     * @param ConnectorInterface $connector
     * @param Utils              $utils
     */
    public function __construct(ConnectorInterface $connector, Utils $utils)
    {

        $this->connector = $connector;
        $this->pdo = $this->connector->getConnection();
        $this->utils = $utils;

    }

    /**
     * @param string $table
     * @param string $sql
     *
     * @return string
     */
    protected function makeSql(string $table, string $sql): string
    {

        return sprintf($sql, $table);

    }

    /**
     * @param string $sql
     * @param array  $parameters
     *
     * @return array
     */
    protected function fetch(string $sql, array $parameters = []): array
    {

        $sth = $this->pdo->prepare($sql);

        $sth->execute($parameters);

        return $sth->fetchAll(PDO::FETCH_ASSOC);

    }

}