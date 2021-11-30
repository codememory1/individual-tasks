<?php

namespace Codememory\Components\IndividualTasks\Interfaces;

/**
 * Interface JobInterface
 *
 * @package Codememory\Components\IndividualTasks\Interfaces
 *
 * @author  Codememory
 */
interface JobInterface
{

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function handler(array $parameters = []): mixed;

}