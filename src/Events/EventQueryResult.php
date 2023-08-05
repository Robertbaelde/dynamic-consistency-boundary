<?php

namespace DynamicConsistencyBoundary\EventStore\Events;

use DynamicConsistencyBoundary\EventStore\Repository\OptimisticLock;

final readonly class EventQueryResult
{
    /**
     * @var Event[]
     */
    private array $events;

    public function __construct(
        public OptimisticLock $optimisticLock,
        Event ...$events)
    {
        $this->events = $events;
    }

    /**
     * @return Event[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
