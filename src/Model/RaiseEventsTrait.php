<?php

namespace App\Model;

use Symfony\Component\EventDispatcher\GenericEvent;

trait RaiseEventsTrait
{
    protected $events = [];

    public function popEvents(): array
    {
        $events = $this->events;

        $this->events = [];

        return $events;
    }

    protected function raise(GenericEvent $event)
    {
        $this->events[] = $event;
    }
}