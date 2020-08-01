<?php

declare(strict_types=1);

namespace Nusje2000\ProcessRunner\Tests;

use Nusje2000\ProcessRunner\Task;
use Nusje2000\ProcessRunner\TaskList;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class TaskListTest extends TestCase
{
    public function testGetTasks(): void
    {
        $idle = new Task('task 1', $this->createProcess(false, false));
        $running = new Task('task 2', $this->createProcess(true, true));
        $successfull = new Task('task 3', $this->createProcess(true, false, 0));
        $completed = new Task('task 4', $this->createProcess(true, false));
        $failed = new Task('task 5', $this->createProcess(true, false, 1));

        $taskList = new TaskList([$idle, $running, $successfull, $completed, $failed]);
        self::assertSame(
            [$idle, $running, $successfull, $completed, $failed],
            $taskList->getTasks()
        );
    }

    public function testGetIdleTasks(): void
    {
        $idle = new Task('task 1', $this->createProcess(false, false));
        $running = new Task('task 2', $this->createProcess(true, true));
        $successfull = new Task('task 3', $this->createProcess(true, false, 0));
        $completed = new Task('task 4', $this->createProcess(true, false));
        $failed = new Task('task 5', $this->createProcess(true, false, 1));

        $taskList = new TaskList([$idle, $running, $successfull, $completed, $failed]);
        self::assertSame(
            [$idle],
            array_values($taskList->getIdleTasks()->getTasks())
        );
    }

    public function testGetStartedTasks(): void
    {
        $idle = new Task('task 1', $this->createProcess(false, false));
        $running = new Task('task 2', $this->createProcess(true, true));
        $successfull = new Task('task 3', $this->createProcess(true, false, 0));
        $completed = new Task('task 4', $this->createProcess(true, false));
        $failed = new Task('task 5', $this->createProcess(true, false, 1));

        $taskList = new TaskList([$idle, $running, $successfull, $completed, $failed]);
        self::assertSame(
            [$running, $successfull, $completed, $failed],
            array_values($taskList->getStartedTasks()->getTasks())
        );
    }

    public function testGetRunningTasks(): void
    {
        $idle = new Task('task 1', $this->createProcess(false, false));
        $running = new Task('task 2', $this->createProcess(true, true));
        $successfull = new Task('task 3', $this->createProcess(true, false, 0));
        $completed = new Task('task 4', $this->createProcess(true, false));
        $failed = new Task('task 5', $this->createProcess(true, false, 1));

        $taskList = new TaskList([$idle, $running, $successfull, $completed, $failed]);
        self::assertSame(
            [$running],
            array_values($taskList->getRunningTasks()->getTasks())
        );
    }

    public function testGetCompletedTasks(): void
    {
        $idle = new Task('task 1', $this->createProcess(false, false));
        $running = new Task('task 2', $this->createProcess(true, true));
        $successfull = new Task('task 3', $this->createProcess(true, false, 0));
        $completed = new Task('task 4', $this->createProcess(true, false));
        $failed = new Task('task 5', $this->createProcess(true, false, 1));

        $taskList = new TaskList([$idle, $running, $successfull, $completed, $failed]);
        self::assertSame(
            [$successfull, $completed, $failed],
            array_values($taskList->getCompletedTasks()->getTasks())
        );
    }

    public function testGetSuccessfullTasks(): void
    {
        $idle = new Task('task 1', $this->createProcess(false, false));
        $running = new Task('task 2', $this->createProcess(true, true));
        $successfull = new Task('task 3', $this->createProcess(true, false, 0));
        $completed = new Task('task 4', $this->createProcess(true, false));
        $failed = new Task('task 5', $this->createProcess(true, false, 1));

        $taskList = new TaskList([$idle, $running, $successfull, $completed, $failed]);
        self::assertSame(
            [$successfull],
            array_values($taskList->getSuccessfullTasks()->getTasks())
        );
    }

    public function testGetFailedTasks(): void
    {
        $idle = new Task('task 1', $this->createProcess(false, false));
        $running = new Task('task 2', $this->createProcess(true, true));
        $successfull = new Task('task 3', $this->createProcess(true, false, 0));
        $completed = new Task('task 4', $this->createProcess(true, false));
        $failed = new Task('task 5', $this->createProcess(true, false, 1));

        $taskList = new TaskList([$idle, $running, $successfull, $completed, $failed]);
        self::assertSame(
            [$failed],
            array_values($taskList->getFailedTasks()->getTasks())
        );
    }

    public function testGount(): void
    {
        $idle = new Task('task 1', $this->createProcess(false, false));
        $running = new Task('task 2', $this->createProcess(true, true));
        $successfull = new Task('task 3', $this->createProcess(true, false, 0));
        $completed = new Task('task 4', $this->createProcess(true, false));
        $failed = new Task('task 5', $this->createProcess(true, false, 1));

        $taskList = new TaskList([$idle, $running, $successfull, $completed, $failed]);
        self::assertSame(5, $taskList->count());
        self::assertCount(5, $taskList);
    }

    private function createProcess(bool $started, bool $running, ?int $exitCode = null): Process
    {
        $process = $this->createStub(Process::class);
        $process->method('isStarted')->willReturn($started);
        $process->method('isRunning')->willReturn($running);
        $process->method('getExitCode')->willReturn($exitCode);

        return $process;
    }
}
