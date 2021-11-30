<?php

namespace Codememory\Components\IndividualTasks\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\IndividualTasks\Utils;
use Codememory\FileSystem\File;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MakeJobCommand
 *
 * @package Codememory\Components\IndividualTasks\Commands
 *
 * @author  Codememory
 */
class MakeJobCommand extends Command
{

    /**
     * @inheritDoc
     */
    protected ?string $command = 'make:job';

    /**
     * @inheritDoc
     */
    protected ?string $description = 'Create a job file with prepared code';

    /**
     * @inheritDoc
     */
    protected function wrapArgsAndOptions(): Command
    {

        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Job name')
            ->addOption('re-create', null, InputOption::VALUE_NONE, 'Recreate job');

        return $this;

    }

    /**
     * @inheritDoc
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $filesystem = new File();
        $utils = new Utils();
        $name = sprintf('%sTask', $input->getArgument('name'));
        $fullPath = $utils->getPathWithTasks() . $name . '.php';

        if ($filesystem->exist($fullPath) && !$input->getOption('re-create')) {
            $this->io->error('The red task already exists. If you want to recreate, run the command with the --re-create option');

            return self::FAILURE;
        }

        file_put_contents($filesystem->getRealPath($fullPath), $this->getStubJob($utils, $name));

        $this->io->success('Task successfully created');

        return self::SUCCESS;

    }

    /**
     * @param Utils  $utils
     * @param string $jobName
     *
     * @return string
     */
    private function getStubJob(Utils $utils, string $jobName): string
    {

        $stub = file_get_contents(__DIR__ . '/Stubs/JobStub.stub');

        return str_replace([
            '{namespace}', '{className}'
        ], [
            rtrim($utils->getNamespaceWithTasks(), '\\'),
            $jobName
        ], $stub);

    }

}