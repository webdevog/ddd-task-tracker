<?php

namespace App\Model;

use Symfony\Component\EventDispatcher\Event;

interface RaiseEventsInterface
{
    /**
     * Return events raised by the entity and clear those.
     *
     * @return Event[]
     */
    public function popEvents(): array;
}