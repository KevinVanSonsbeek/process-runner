<?php

declare(strict_types=1);

namespace Nusje2000\ProcessRunner;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Process\Process;

final class Task
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Process
     */
    private $process;

    public function __construct(string $name, Process $process)
    {
        $this->id = Uuid::uuid4();
        $this->name = $name;
        $this->process = $process;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProcess(): Process
    {
        return $this->process;
    }

    public function isIdle(): bool
    {
        return !$this->process->isStarted();
    }

    public function isStarted(): bool
    {
        return $this->process->isStarted();
    }

    public function isRunning(): bool
    {
        return $this->isStarted() && $this->process->isRunning();
    }

    public function isCompleted(): bool
    {
        return $this->isStarted() && !$this->process->isRunning();
    }

    public function isSuccessfull(): bool
    {
        return $this->isCompleted() && 0 === $this->process->getExitCode();
    }

    public function isFailed(): bool
    {
        if (null === $this->process->getExitCode()) {
            return false;
        }

        return $this->isCompleted() && 0 !== $this->process->getExitCode();
    }
}
