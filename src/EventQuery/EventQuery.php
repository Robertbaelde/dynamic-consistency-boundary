<?php

namespace DynamicConsistencyBoundary\EventStore\EventQuery;

use DynamicConsistencyBoundary\EventStore\Events\Event;

final readonly class EventQuery
{
    /**
     * @var Query[]
     */
    public array $queries;

    public function __construct(
        Query ...$queries
    )
    {
        $this->queries = $queries;
    }

    public function mergeWith(EventQuery $eventQuery): EventQuery
    {
        // todo: add deduplication
        return new EventQuery(...array_merge($this->queries, $eventQuery->queries));
    }

    public function matches(Event $event): bool
    {
        foreach ($this->queries as $query) {
            if ($query->matches($event)) {
                return true;
            }
        }
        return false;
    }
}
