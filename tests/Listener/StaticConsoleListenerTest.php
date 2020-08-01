<?php

declare(strict_types=1);

namespace Nusje2000\ProcessRunner\Tests\Listener;

use Nusje2000\ProcessRunner\Listener\StaticConsoleListener;
use Nusje2000\ProcessRunner\Task;
use Nusje2000\ProcessRunner\TaskList;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Process\Process;

final class StaticConsoleListenerTest extends TestCase
{
    public function testConstuctWithStyleConfiguration(): void
    {
        $formatter = $this->createMock(OutputFormatterInterface::class);
        $formatter->expects(self::exactly(4))->method('setStyle')->withConsecutive(
            ['error', new IsInstanceOf(OutputFormatterStyle::class)],
            ['success', new IsInstanceOf(OutputFormatterStyle::class)],
            ['idle', new IsInstanceOf(OutputFormatterStyle::class)],
            ['running', new IsInstanceOf(OutputFormatterStyle::class)]
        );

        $output = $this->createMock(ConsoleOutput::class);
        $output->expects(self::once())->method('getFormatter')->willReturn($formatter);

        new StaticConsoleListener($output);
    }

    public function testOnTick(): void
    {
        $output = $this->createMock(ConsoleOutput::class);
        $consoleListener = new StaticConsoleListener($output, false);

        $section = $this->createMock(ConsoleSectionOutput::class);
        $output->expects(self::exactly(2))->method('writeln')->withConsecutive([
            [
                'task 2 is <running>running</running>',
                'task 3 is <success>successfull</success>',
                'task 5 has <error>failed</error>',
                'task 6 has <error>failed</error>',
                'Output:',
                'some error from normal output',
                '',
                'task 7 has <error>failed</error>',
                'Error output:',
                'some error from error output',
                '',
            ],
        ], [
            [],
        ]);

        $taskList = new TaskList([
            new Task('task 1', $this->createProcess(false, false)),
            new Task('task 2', $this->createProcess(true, true)),
            new Task('task 3', $this->createProcess(true, false, 0)),
            new Task('task 4', $this->createProcess(true, false)),
            new Task('task 5', $this->createProcess(true, false, 1)),
            new Task('task 6', $this->createProcess(true, false, 2, 'some error from normal output')),
            new Task('task 7', $this->createProcess(true, false, 3, '', 'some error from error output')),
        ]);

        $consoleListener->onTick($taskList);
        $consoleListener->onTick($taskList);
    }

    public function testGetPriority(): void
    {
        $consoleListener = new StaticConsoleListener($this->createStub(ConsoleOutput::class), false);
        self::assertSame(0, $consoleListener->getPriority());

        $consoleListener = new StaticConsoleListener($this->createStub(ConsoleOutput::class), false, 100);
        self::assertSame(100, $consoleListener->getPriority());
    }

    private function createProcess(bool $started, bool $running, ?int $exitCode = null, string $output = '', string $errorOutput = ''): Process
    {
        $process = $this->createStub(Process::class);
        $process->method('isStarted')->willReturn($started);
        $process->method('isRunning')->willReturn($running);
        $process->method('getExitCode')->willReturn($exitCode);
        $process->method('getOutput')->willReturn($output);
        $process->method('getErrorOutput')->willReturn($errorOutput);

        return $process;
    }
}
