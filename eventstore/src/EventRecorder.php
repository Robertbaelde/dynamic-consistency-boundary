<?php

namespace DynamicConsistencyBoundary\EventStore;

use DynamicConsistencyBoundary\EventStore\EventQuery\EventQuery;
use DynamicConsistencyBoundary\EventStore\Events\Event;
use DynamicConsistencyBoundary\EventStore\Events\EventQueryResult;
use DynamicConsistencyBoundary\EventStore\Repository\NullOptimisticLock;
use DynamicConsistencyBoundary\EventStore\Repository\OptimisticLock;

final class EventRecorder
{
    private array $eventsToAppend = [];

    private function __construct(
        public OptimisticLock $optimisticLock,
    )
    {
    }

    public static function fromQueryResult(OptimisticLock $optimisticLock): self
    {
        return new self($optimisticLock);
    }

    public static function withoutConstraints(): self
    {
        return new self(new NullOptimisticLock());
    }

    public function record(Event ...$events): void
    {
        foreach ($events as $event) {
            $this->eventsToAppend[] = $event;
        }
    }

    /**
     * @return array<Event>
     */
    public function getEventsToAppend(): array
    {
        return $this->eventsToAppend;
    }
}
