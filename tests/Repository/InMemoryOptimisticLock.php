<?php

namespace DynamicConsistencyBoundary\EventStore\Tests\Repository;

use DynamicConsistencyBoundary\EventStore\EventQuery\EventQuery;
use DynamicConsistencyBoundary\EventStore\Events\EventQueryResult;
use DynamicConsistencyBoundary\EventStore\Repository\OptimisticLock;

final readonly class InMemoryOptimisticLock implements OptimisticLock
{
    public function __construct(
        public EventQuery $eventQuery,
        public int $expectedEventCount,
    )
    {
    }
}
