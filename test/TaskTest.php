<?php

namespace Amp\Test;

use Amp\PHPUnit\TestCase;
use Concurrent\Task;
use function Amp\delay;

class TaskTest extends TestCase
{
    public function testSequentialAwait(): void
    {
        delay(1);
        delay(1);

        $this->assertTrue(true);
    }

    public function testAsync(): void
    {
        $awaitable = Task::async('Amp\\delay', 100);
        delay(100);

        $this->assertTrue(true);

        Task::await($awaitable);
    }

    public function testAsyncMultiAwait(): void
    {
        Task::async(function () use (&$awaitable) {
            $awaitable = Task::async('Amp\\delay', 100);
        });

        Task::async(function () use (&$awaitable) {
            Task::await($awaitable);
        });

        Task::async(function () use (&$awaitable) {
            Task::await($awaitable);
        });

        $this->assertTrue(true);
    }
}
