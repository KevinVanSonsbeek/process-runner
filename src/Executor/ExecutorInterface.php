<?php

declare(strict_types=1);

namespace Nusje2000\ProcessRunner\Executor;

use Nusje2000\ProcessRunner\Listener\ExecutionListener;
use Nusje2000\ProcessRunner\TaskList;

interface ExecutorInterface
{
    /**
     * @return array<ExecutionListener>
     */
    public function getListeners(): array;

    public function addListener(ExecutionListener $listener): void;

    public function removeListener(ExecutionListener $listener): void;

    public function execute(TaskList $taskList): void;
}
