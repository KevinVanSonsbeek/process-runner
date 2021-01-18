<?php

declare(strict_types=1);

namespace Nusje2000\ProcessRunner;

use ArrayIterator;
use Countable;
use Iterator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class TaskList implements Countable
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var array<Task>
     */
    private $tasks;

    /**
     * @param array<Task> $tasks
     */
    public function __construct(array $tasks)
    {
        $this->id = Uuid::uuid4();
        $this->tasks = $tasks;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
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

    /**
     * @psalm-param callable(Task):boolean $filterFunction
     */
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
