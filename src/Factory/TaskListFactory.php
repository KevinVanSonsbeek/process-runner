<?php

declare(strict_types=1);

namespace Nusje2000\ParallelProcess\Factory;

use Nusje2000\ParallelProcess\Task;
use Nusje2000\ParallelProcess\TaskList;
use Symfony\Component\Process\Process;

final class TaskListFactory
{
    /**
     * Create a TaskList based on an array of commands
     *
     * When a non integer key is used, that key will be the name of the task.
     *
     * @param array<int|string, string> $commands
     */
    public static function createFromArray(array $commands): TaskList
    {
        $tasks = [];
        foreach ($commands as $nameOrIndex => $command) {
            $tasks[] = new Task(
                is_int($nameOrIndex) ? $command : $nameOrIndex,
                Process::fromShellCommandline($command)
            );
        }

        return new TaskList($tasks);
    }
}
