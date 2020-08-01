<?php

declare(strict_types=1);

namespace Nusje2000\ProcessRunner\Tests\Listener;

use Nusje2000\ProcessRunner\Listener\ConsoleListener;
use Nusje2000\ProcessRunner\Task;
use Nusje2000\ProcessRunner\TaskList;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Process\Process;

final class ConsoleListenerTest extends TestCase
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

        new ConsoleListener($output);
    }

    public function testOnTick(): void
    {
        $output = $this->createMock(ConsoleOutput::class);
        $consoleListener = new ConsoleListener($output, false);

        $section = $this->createMock(ConsoleSectionOutput::class);
        $section->expects(self::exactly(2))->method('clear');
        $section->expects(self::exactly(2))->method('writeln')->with([
            'task 1 (<idle>idle</idle>)',
            'task 2 (<running>running</running>)',
            'task 3 (<success>success</success>)',
            'task 4 (unknown process status)',
            'task 5 (<error>failed (exit code: 1)</error>)',
            'task 6 (<error>failed (exit code: 2)</error>)',
            'task 7 (<error>failed (exit code: 3)</error>)',
            '',
            '<error>Task "task 5" failed (exit code: 1).</error>',
            '',
            '<error>Task "task 6" failed (exit code: 2).</error>',
            'Output:',
            'some error from normal output',
            '',
            '<error>Task "task 7" failed (exit code: 3).</error>',
            'Error output:',
            'some error from error output',
            '',
        ]);

        $output->expects(self::once())->method('section')->willReturn($section);

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
        $consoleListener = new ConsoleListener($this->createStub(ConsoleOutput::class), false);
        self::assertSame(0, $consoleListener->getPriority());

        $consoleListener = new ConsoleListener($this->createStub(ConsoleOutput::class), false, 100);
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
