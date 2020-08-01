<?php

declare(strict_types=1);

namespace Nusje2000\ProcessRunner\Executor;

use InvalidArgumentException;
use Nusje2000\ProcessRunner\TaskList;

final class ParallelExecutor extends AbstractExecutor
{
    /**
     * @var int
     */
    protected $refreshRate;

    /**
     * @param int $refreshRate The amount of times per second the process state are evaluated
     */
    public function __construct(int $refreshRate = 5)
    {
        if ($refreshRate < 1) {
            throw new InvalidArgumentException('Refresh rate must be at least 1.');
        }

        $this->refreshRate = $refreshRate;
    }

    public function execute(TaskList $taskList): void
    {
        foreach ($taskList->getIdleTasks()->getIterator() as $task) {
            $task->getProcess()->start();
        }

        while (true) {
            $runningTasks = $taskList->getRunningTasks();
            $this->triggerOnTick($taskList);

            if (0 === $runningTasks->count()) {
                break;
            }

            usleep(1000000 / $this->refreshRate);
        }
    }
}
