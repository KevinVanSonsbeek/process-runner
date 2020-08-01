<?php

declare(strict_types=1);

use Nusje2000\ProcessRunner\Executor\ParallelExecutor;
use Nusje2000\ProcessRunner\Executor\SequentialExecutor;
use Nusje2000\ProcessRunner\Factory\TaskListFactory;
use Nusje2000\ProcessRunner\Listener\ConsoleListener;
use Nusje2000\ProcessRunner\Listener\StaticConsoleListener;
use Nusje2000\ProcessRunner\TaskList;
use Symfony\Component\Console\Output\ConsoleOutput;

$cwd = getcwd();
if (false === $cwd) {
    throw new UnexpectedValueException('Could not resolve the current working directory.');
}

require_once $cwd . '/vendor/autoload.php';

$output = new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, true);
$consoleListener = new ConsoleListener($output);
$staticConsoleListener = new StaticConsoleListener($output);

function createTasklist(): TaskList
{
    return TaskListFactory::createFromArray([
        0 => 'echo "Hello world!"', // This item will be named after the command
        'process name 1' => 'php -r "sleep(1);"',
        'process name 2' => 'php -r "sleep(2);"',
        'process name 3' => 'php -r "throw new \Exception(\'Some exception message\');"',
    ]);
}

$output->writeln('<comment>Exectution using the parallel executor and logging with ConsoleListener.</comment>');
$parallelExecutor = new ParallelExecutor();
$parallelExecutor->addListener($consoleListener);
$parallelExecutor->execute(createTasklist());
$output->write(PHP_EOL);

$output->writeln('<comment>Exectution using the sequential executor and logging with ConsoleListener.</comment>');
$sequentialExecutor = new SequentialExecutor();
$sequentialExecutor->addListener($consoleListener);
$sequentialExecutor->execute(createTasklist());
$output->write(PHP_EOL);

$output->writeln('<comment>Exectution using the parallel executor and logging with StaticConsoleListener.</comment>');
$parallelExecutor = new ParallelExecutor();
$parallelExecutor->addListener($staticConsoleListener);
$parallelExecutor->execute(createTasklist());
$output->write(PHP_EOL);

$output->writeln('<comment>Exectution using the sequential executor and logging with StaticConsoleListener.</comment>');
$sequentialExecutor = new SequentialExecutor();
$sequentialExecutor->addListener($staticConsoleListener);
$sequentialExecutor->execute(createTasklist());
$output->write(PHP_EOL);
