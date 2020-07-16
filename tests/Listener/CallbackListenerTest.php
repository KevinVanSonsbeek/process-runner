<?php

declare(strict_types=1);

namespace Nusje2000\ParallelProcess\Tests\Listener;

use Nusje2000\ParallelProcess\Listener\CallbackListener;
use Nusje2000\ParallelProcess\TaskList;
use PHPUnit\Framework\TestCase;

final class CallbackListenerTest extends TestCase
{
    public function testOnTick(): void
    {
        $called = false;

        $expectedTaskList = new TaskList([]);
        $listener = new CallbackListener(
            static function (TaskList $taskList) use (&$called, $expectedTaskList) {
                self::assertSame($expectedTaskList, $taskList);
                $called = true;
            }
        );
        $listener->onTick($expectedTaskList);

        self::assertTrue($called);
    }

    public function testGetPriority(): void
    {
        $listener = new CallbackListener(
            static function () {
            }
        );
        self::assertSame(0, $listener->getPriority());

        $listener = new CallbackListener(
            static function () {
            },
            100
        );
        self::assertSame(100, $listener->getPriority());
    }
}
