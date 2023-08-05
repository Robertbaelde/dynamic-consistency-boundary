<?php

namespace DynamicConsistencyBoundary\EventStore\Tests\Repository;

use DynamicConsistencyBoundary\EventStore\EventQuery\EventQuery;
use DynamicConsistencyBoundary\EventStore\EventRecorder;
use DynamicConsistencyBoundary\EventStore\Events\Event;
use DynamicConsistencyBoundary\EventStore\Events\EventQueryResult;
use DynamicConsistencyBoundary\EventStore\Repository\EventRepositoryInterface;
use DynamicConsistencyBoundary\EventStore\Repository\NullOptimisticLock;

final class InMemoryEventRepository implements EventRepositoryInterface
{
    private $events = [];
    public function __construct(
    )
    {
    }

    public function query(EventQuery $eventQuery): EventQueryResult
    {
        $events = array_filter($this->events, function (Event $event) use ($eventQuery) {
            foreach($eventQuery->queries as $query){
                if($query->matches($event)){
                    return true;
                }
            }
            return false;
        });
        return new EventQueryResult(
            new InMemoryOptimisticLock(eventQuery: $eventQuery, expectedEventCount: count($events)),
            ...$events
        );
    }

    public function commitWithoutGuard(Event ...$events): void
    {
        foreach ($events as $event) {
            $this->events[] = $event;
        }
    }

    public function commit(EventRecorder $eventRecorder): void
    {
        if($eventRecorder->optimisticLock instanceof NullOptimisticLock){
            $this->commitWithoutGuard(...$eventRecorder->getEventsToAppend());
            return;
        }

        $lock = $eventRecorder->optimisticLock;
        if(!$lock instanceof InMemoryOptimisticLock){
            throw new \Exception("Invalid lock type");
        }

        // in memory lock is simple, just check if the event count is the same
        if (count($this->query($lock->eventQuery)->getEvents()) !== $lock->expectedEventCount) {
            throw new \Exception("Concurrency error");
        }

        foreach ($eventRecorder->getEventsToAppend() as $event) {
            $this->events[] = $event;
        }
    }
}
