<?php

namespace DynamicConsistencyBoundary\EventStore;

use DynamicConsistencyBoundary\EventStore\EventQuery\EventQuery;
use DynamicConsistencyBoundary\EventStore\Guards\Guard;
use DynamicConsistencyBoundary\EventStore\Repository\EventRepositoryInterface;

final readonly class EventStore
{
    public function __construct(
        private EventRepositoryInterface $eventRepository
    )
    {
    }

    public function prepare(Guard ...$guards): EventRecorder
    {
        $eventQuery = new EventQuery();

        // Create Event Query from guards
        foreach ($guards as $guard) {
            $eventQuery = $eventQuery->mergeWith($guard->getEventQuery());
        }

        $queryResult = $this->eventRepository->query($eventQuery);
        foreach($queryResult->getEvents() as $event){
            foreach ($guards as $guard) {
                if($guard->getEventQuery()->matches($event)){
                    $guard->apply($event);
                }
            }
        }

        return EventRecorder::fromQueryResult(
            $queryResult->optimisticLock,
        );
    }

    public function commit(EventRecorder $eventRecorder): void
    {
        $this->eventRepository->commit($eventRecorder);
    }
}
