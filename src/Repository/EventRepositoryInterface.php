<?php

namespace DynamicConsistencyBoundary\EventStore\Repository;

use DynamicConsistencyBoundary\EventStore\EventQuery\EventQuery;
use DynamicConsistencyBoundary\EventStore\EventRecorder;
use DynamicConsistencyBoundary\EventStore\Events\Event;
use DynamicConsistencyBoundary\EventStore\Events\EventQueryResult;

interface EventRepositoryInterface
{
    public function query(EventQuery $eventQuery): EventQueryResult;

    public function commitWithoutGuard(Event ...$events): void;

    public function commit(EventRecorder $eventRecorder): void;
}
