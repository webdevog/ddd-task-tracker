<?php

namespace App\Entity;

use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGetId()
    {
        $task = new Task();

        $this->assertIsString($task->getId());
    }

    public function testGetTitle()
    {
        $task = new Task();
        $expected = 'Test title';
        $task->setTitle($expected);

        $this->assertEquals($expected, $task->getTitle());
    }

    public function testGetCreatedAt()
    {
        $task = new Task();

        $this->assertInstanceOf(\DateTimeImmutable::class, $task->getCreatedAt());
    }

    public function testGetStatus()
    {
        $task = new Task();
        $expected = Task::STATUS_NEW;

        $this->assertEquals($expected, $task->getStatus());

        $task->setStatus(Task::STATUS_COMPLETED);
        $this->assertEquals(Task::STATUS_COMPLETED, $task->getStatus());
    }
}
