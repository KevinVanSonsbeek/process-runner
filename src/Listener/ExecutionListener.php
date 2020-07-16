<?php

declare(strict_types=1);

namespace Nusje2000\ParallelProcess\Listener;

use Nusje2000\ParallelProcess\TaskList;

interface ExecutionListener
{
    /**
     * Each time the processes are checked for status updates, this function will be triggered.
     */
    public function onTick(TaskList $taskList): void;

    /**
     * Defines the priority of the listener, higher priority means earlier exection of the onTick function
     */
    public function getPriority(): int;
}
