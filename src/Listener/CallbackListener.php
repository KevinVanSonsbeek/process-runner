<?php

declare(strict_types=1);

namespace Nusje2000\ProcessRunner\Listener;

use Nusje2000\ProcessRunner\TaskList;

final class CallbackListener implements ExecutionListener
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @var int
     */
    private $priority;

    public function __construct(callable $callback, int $priority = 0)
    {
        $this->callback = $callback;
        $this->priority = $priority;
    }

    public function onTick(TaskList $taskList): void
    {
        call_user_func($this->callback, $taskList);
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
