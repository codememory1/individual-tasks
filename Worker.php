<?php

namespace Codememory\Components\IndividualTasks;

use Codememory\Components\Console\IO;
use Codememory\Components\Database\Pack\DatabasePack;
use Codememory\Components\IndividualTasks\Interfaces\WorkerInterface;
use Codememory\Components\IndividualTasks\Repository\JobRepository;
use JetBrains\PhpStorm\NoReturn;
use PDO;

/**
 * Class Worker
 *
 * @package Codememory\Components\IndividualTasks
 *
 * @author  Codememory
 */
class Worker implements WorkerInterface
{

    /**
     * @var DatabasePack
     */
    private DatabasePack $databasePack;

    /**
     * @var PDO
     */
    private PDO $pdo;

    /**
     * @var Utils
     */
    private Utils $utils;

    /**
     * @var JobRepository
     */
    private JobRepository $jobRepository;

    /**
     * @param DatabasePack $databasePack
     */
    public function __construct(DatabasePack $databasePack)
    {

        $connector = $databasePack->getConnectionWorker()->getConnector();
        $this->pdo = $connector->getConnection();
        $this->utils = new Utils();
        $this->databasePack = $databasePack;
        $this->jobRepository = new JobRepository($connector, $this->utils);

    }

    /**
     * @param IO $IO
     *
     * @return bool
     */
    public function daemon(IO $IO): bool
    {

        $IO->success('The daemon started successfully');

        pcntl_signal(SIGTERM, [$this, 'signalCompleted']);
        pcntl_signal(SIGINT, [$this, 'signalCompleted']);
        pcntl_signal(SIGHUP, [$this, 'signalCompleted']);
        pcntl_signal(SIGQUIT, [$this, 'signalCompleted']);

        while (true) {
            foreach ($this->jobRepository->findAll() as $job) {
                $this->pdo
                    ->prepare(sprintf('UPDATE `%s` SET `key` = :key, `value` = :value', $this->utils->getTableWithInfo()))
                    ->execute([
                        'key'   => 'status',
                        'value' => 1
                    ]);

                $jobObject = new $job['name']($this->databasePack);

                // Calling the job handler
                $jobObject->handler(json_decode($job['payload'], true));

                // Removing a completed job from a table
                $this->deleteJob($job);
            }
        }

    }

    /**
     * @return void
     */
    #[NoReturn]
    private function signalCompleted(): void
    {

        $this->pdo
            ->prepare(sprintf('UPDATE `%s` SET `key` = :key, `value` = :value', $this->utils->getTableWithInfo()))
            ->execute([
                'key'   => 'status',
                'value' => 0
            ]);

        exit;

    }

    /**
     * @param array $jobData
     *
     * @return void
     */
    private function deleteJob(array $jobData): void
    {

        $this->pdo
            ->prepare(sprintf('DELETE FROM `%s` WHERE `id` = :id', $this->utils->getTableWithTasks()))
            ->execute(['id' => $jobData['id']]);

    }

}