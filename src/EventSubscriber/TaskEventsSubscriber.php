<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Model\Event\TaskCreated;
use App\Model\Event\TaskUpdated;
use App\Model\Event\TaskRemoved;
use Psr\Log\LoggerInterface;

class TaskEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $appLogger;

    public function __construct(LoggerInterface $appLogger)
    {
        $this->appLogger = $appLogger;
    }

    public function onTaskCreated(TaskCreated $event)
    {
        // Here we can do any action we want, for example send an email about newly created task
        $this->appLogger->info('CALLED METHOD: ' . __METHOD__, ['taskId' => $event->getTask()->getId()]);
    }

    public function onTaskUpdated(TaskUpdated $event)
    {
        $this->appLogger->info('CALLED METHOD: ' . __METHOD__, ['taskId' => $event->getTask()->getId()]);
    }

    public function onTaskRemoved(TaskRemoved $event)
    {
        $this->appLogger->info('CALLED METHOD: ' . __METHOD__, ['taskId' => $event->getTask()->getId()]);
    }

    public static function getSubscribedEvents()
    {
        return [
            TaskCreated::class => 'onTaskCreated',
            TaskUpdated::class => 'onTaskUpdated',
            TaskRemoved::class => 'onTaskRemoved',
        ];
    }
}
