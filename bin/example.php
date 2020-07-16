<?php

declare(strict_types=1);

use Nusje2000\ParallelProcess\Executor\ParallelExecutor;
use Nusje2000\ParallelProcess\Executor\SequentialExecutor;
use Nusje2000\ParallelProcess\Factory\TaskListFactory;
use Nusje2000\ParallelProcess\Listener\ConsoleListener;
use Nusje2000\ParallelProcess\TaskList;
use Symfony\Component\Console\Output\ConsoleOutput;

$cwd = getcwd();
if (false === $cwd) {
    throw new UnexpectedValueException('Could not resolve the current working directory.');
}

require_once $cwd . '/vendor/autoload.php';

$output = new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, true);
$consoleListener = new ConsoleListener($output);

function createTasklist(): TaskList
{
    return TaskListFactory::createFromArray([
        0 => 'echo "Hello world!"', // This item will be named after the command
        'process name 1' => 'php -r "sleep(1);"',
        'process name 2' => 'php -r "sleep(2);"',
        'process name 3' => 'php -r "throw new \Exception(\'error\');"',
    ]);
}

$output->writeln('<comment>Exectution using the parallel executor.</comment>');
$parallelExecutor = new ParallelExecutor();
$parallelExecutor->addListener($consoleListener);
$parallelExecutor->execute(createTasklist());

$output->writeln('<comment>Exectution using the sequential executor.</comment>');
$sequentialExecutor = new SequentialExecutor();
$sequentialExecutor->addListener($consoleListener);
$sequentialExecutor->execute(createTasklist());
