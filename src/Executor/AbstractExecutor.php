<?php

declare(strict_types=1);

namespace Nusje2000\ParallelProcess\Executor;

use Nusje2000\ParallelProcess\Listener\ExecutionListener;
use Nusje2000\ParallelProcess\TaskList;

abstract class AbstractExecutor implements ExecutorInterface
{
    /**
     * @var array<ExecutionListener>
     */
    private $listeners;

    public function getListeners(): array
    {
        return $this->listeners;
    }

    public function addListener(ExecutionListener $listener): void
    {
        $this->listeners[] = $listener;

        uasort($this->listeners, static function (ExecutionListener $base, ExecutionListener $compare) {
            return $compare->getPriority() <=> $base->getPriority();
        });
    }

    public function removeListener(ExecutionListener $listener): void
    {
        $index = array_search($listener, $this->listeners, true);

        if (false !== $index) {
            unset($this->listeners[$index]);
        }
    }

    protected function triggerOnTick(TaskList $taskList): void
    {
        foreach ($this->getListeners() as $listener) {
            $listener->onTick($taskList);
        }
    }
}
