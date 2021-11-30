<?php

namespace Codememory\Components\IndividualTasks\Repository;

/**
 * Class JobRepository
 *
 * @package Codememory\Components\IndividualTasks\Repository
 *
 * @author  Codememory
 */
class JobRepository extends AbstractRepository
{

    /**
     * @return array
     */
    public function findAll(): array
    {

        $sql = $this->makeSql($this->utils->getTableWithTasks(), 'SELECT * FROM `%s`');

        return $this->fetch($sql);

    }

}