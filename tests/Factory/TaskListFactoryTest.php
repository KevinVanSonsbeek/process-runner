<?php

declare(strict_types=1);

namespace Nusje2000\ProcessRunner\Tests\Factory;

use Nusje2000\ProcessRunner\Factory\TaskListFactory;
use PHPUnit\Framework\TestCase;

final class TaskListFactoryTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $taskList = TaskListFactory::createFromArray([
            0 => 'command 1',
            'task 2' => 'command 2',
            2 => 'command 3',
            'task 4' => 'command 4',
        ]);

        $mapped = [];
        foreach ($taskList->getIterator() as $task) {
            $mapped[$task->getName()] = $task->getProcess()->getCommandLine();
        }

        self::assertSame([
            'command 1' => 'command 1',
            'task 2' => 'command 2',
            'command 3' => 'command 3',
            'task 4' => 'command 4',
        ], $mapped);
    }
}
