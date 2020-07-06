<?php

namespace App\Model\Event;

use App\Entity\Task;
use Symfony\Component\EventDispatcher\GenericEvent;

class TaskRemoved extends GenericEvent
{
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function getTask(): Task
    {
        return $this->task;
    }
}