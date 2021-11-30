<?php

namespace Codememory\Components\IndividualTasks\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\IndividualTasks\Utils;
use Codememory\FileSystem\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListJobsCommand
 *
 * @package Codememory\Components\IndividualTasks\Commands
 *
 * @author  Codememory
 */
class ListJobsCommand extends Command
{

    /**
     * @inheritDoc
     */
    protected ?string $command = 'it:list-jobs';

    /**
     * @inheritDoc
     */
    protected ?string $description = 'Get a list of tasks';

    /**
     * @inheritDoc
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $filesystem = new File();
        $utils = new Utils();
        $jobs = [];

        foreach ($filesystem->scanning($utils->getPathWithTasks()) as $path) {
            $jobs[] = [
                explode('.', $filesystem->basename($path))[0],
                $utils->getPathWithTasks().$path
            ];
        }

        $this->io->table(
            ['name', 'path'],
            $jobs
        );

        return self::SUCCESS;

    }

}