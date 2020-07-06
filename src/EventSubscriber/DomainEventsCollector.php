<?php

namespace App\EventSubscriber;

use App\Model\RaiseEventsInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;
use Psr\Log\LoggerInterface;

class DomainEventsCollector implements EventSubscriber
{
    /**
     * @var Event[] Domain events that are queued
     */
    private $events = [];

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    private $appLogger;

    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $appLogger)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->appLogger = $appLogger;
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->doCollect($event);
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->doCollect($event);
    }

    /**
     * Need to listen to preRemove if soft deletion is used from Doctrine extensions,
     * because it prevents postRemove from being called.
     *
     * @param LifecycleEventArgs $event
     */
    public function preRemove(LifecycleEventArgs $event)
    {
        $this->doCollect($event);
    }

    public function postRemove(LifecycleEventArgs $event)
    {
        echo __METHOD__;
        $this->doCollect($event);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postUpdate,
            Events::preRemove,
            Events::postRemove,
            Events::postPersist,
            Events::postFlush,
        ];
    }

    /**
     * Returns all collected events and then clear those.
     *
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        $this->appLogger->info('CALLED METHOD: ' . __METHOD__);
        $events = $this->events;
        $this->events = [];

        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        // Support case when listeners emitted some new events!
        if ($this->events) {
            $this->dispatchCollectedEvents();
        }
    }

    private function doCollect(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof RaiseEventsInterface) {
            return;
        }

        foreach ($entity->popEvents() as $event) {
            // Hash as a key is used here to prevent any kind of duplication of events
            $this->events[spl_object_hash($event)] = $event;
        }
    }
}