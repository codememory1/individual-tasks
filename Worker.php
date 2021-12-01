<?php

namespace Codememory\Components\IndividualTasks;

use Codememory\Components\Console\IO;
use Codememory\Components\Database\Pack\DatabasePack;
use Codememory\Components\IndividualTasks\Interfaces\WorkerInterface;
use Codememory\Components\IndividualTasks\Repository\JobRepository;
use Codememory\Container\ServiceProvider\Interfaces\ServiceProviderInterface;
use Generator;
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
     * @var ServiceProviderInterface
     */
    private ServiceProviderInterface $serviceProvider;

    /**
     * @param DatabasePack             $databasePack
     * @param ServiceProviderInterface $serviceProvider
     */
    public function __construct(DatabasePack $databasePack, ServiceProviderInterface $serviceProvider)
    {

        $connector = $databasePack->getConnectionWorker()->getConnector();
        $this->pdo = $connector->getConnection();
        $this->utils = new Utils();
        $this->databasePack = $databasePack;
        $this->jobRepository = new JobRepository($connector, $this->utils);
        $this->serviceProvider = $serviceProvider;

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
            $providersForAllTasks = $this->getProvidersForAllTasks();

            foreach ($this->iteration($this->jobRepository->findAll()) as $job) {
                $taskProviders = [];

                foreach (json_decode($job['providers'], true) as $provider) {
                    $taskProviders[] = $providersForAllTasks[$provider];
                }

                $this->updateStatus(1);

                $jobObject = new $job['name']($this->databasePack, $this->serviceProvider);

                // Calling the job handler
                $jobObject->handler(json_decode($job['payload'], true), ...$taskProviders);

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

        $this->updateStatus(0);

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

    /**
     * @param int $status
     *
     * @return void
     */
    private function updateStatus(int $status): void
    {

        $this->pdo
            ->prepare(sprintf('UPDATE `%s` SET `key` = :key, `value` = :value', $this->utils->getTableWithInfo()))
            ->execute([
                'key'   => 'status',
                'value' => $status
            ]);

    }

    /**
     * @param array $data
     *
     * @return Generator
     */
    private function iteration(array $data): Generator
    {

        foreach ($data as $value) {
            yield $value;
        }

    }

    /**
     * @return array
     */
    private function getProvidersForAllTasks(): array
    {

        $providers = [];

        foreach ($this->iteration($this->jobRepository->findAll()) as $job) {
            foreach (json_decode($job['providers'], true) as $provider) {
                if(!array_key_exists($provider, $providers)) {
                    $providers[$provider] = $this->serviceProvider->get($provider);
                }
            }
        }

        return $providers;

    }

}