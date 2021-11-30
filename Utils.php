<?php

namespace Codememory\Components\IndividualTasks;

use Codememory\Components\Configuration\Configuration;
use Codememory\Components\Configuration\Interfaces\ConfigInterface;
use Codememory\Components\GlobalConfig\GlobalConfig;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Utils
 *
 * @package Codememory\Components\IndividualTasks
 *
 * @author  Codememory
 */
class Utils
{

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    public function __construct()
    {

        $this->config = Configuration::getInstance()->open(GlobalConfig::get('individual-tasks.configName'), $this->defaultConfig());

    }

    /**
     * @return string
     */
    public function getPathWithTasks(): string
    {

        return trim($this->config->get('pathWithTasks'), '/') . '/';

    }

    /**
     * @return string
     */
    public function getNamespaceWithTasks(): string
    {

        return trim($this->config->get('namespaceWithTasks'), '\\') . '\\';

    }

    /**
     * @return string
     */
    public function getTableWithTasks(): string
    {

        return $this->config->get('tableWithTasks');

    }

    /**
     * @return string
     */
    public function getTableWithInfo(): string
    {

        return $this->config->get('tableWithInfo');

    }

    /**
     * @return array
     */
    #[ArrayShape([
        'pathWithTasks'        => "string",
        'namespaceWithTasks'   => "string",
        'tableWithTasks'       => "string",
        'tableWithInfo'        => "string"
    ])]
    private function defaultConfig(): array
    {

        return [
            'pathWithTasks'        => GlobalConfig::get('individual-tasks.pathWithTasks'),
            'namespaceWithTasks'   => GlobalConfig::get('individual-tasks.namespaceWithTasks'),
            'tableWithTasks'       => GlobalConfig::get('individual-tasks.tableWithTasks'),
            'tableWithInfo'        => GlobalConfig::get('individual-tasks.tableWithInfo'),
        ];

    }

}