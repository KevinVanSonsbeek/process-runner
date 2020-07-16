<?php

declare(strict_types=1);

namespace Nusje2000\ParallelProcess;

use ArrayIterator;
use Countable;
use Iterator;

final class TaskList implements Countable
{
    /**
     * @var array<Task>
     */
    protected $tasks;

    /**
     * @param array<Task> $tasks
     */
    public function __construct(array $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * @return array<Task>
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function getIdleTasks(): self
    {
        return $this->filter(static function (Task $task) {
            return $task->isIdle();
        });
    }

    public function getStartedTasks(): self
    {
        return $this->filter(static function (Task $task) {
            return $task->isStarted();
        });
    }

    public function getRunningTasks(): self
    {
        return $this->filter(static function (Task $task) {
            return $task->isRunning();
        });
    }

    public function getCompletedTasks(): self
    {
        return $this->filter(static function (Task $task) {
            return $task->isCompleted();
        });
    }

    public function getSuccessfullTasks(): self
    {
        return $this->filter(static function (Task $task) {
            return $task->isSuccessfull();
        });
    }

    public function getFailedTasks(): self
    {
        return $this->filter(static function (Task $task) {
            return $task->isFailed();
        });
    }

    public function filter(callable $filterFunction): self
    {
        return new self(array_filter($this->tasks, $filterFunction));
    }

    public function count(): int
    {
        return count($this->tasks);
    }

    /**
     * @return Iterator<int, Task>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->tasks);
    }
}
