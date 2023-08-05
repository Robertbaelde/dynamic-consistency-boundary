<?php

namespace DynamicConsistencyBoundary\EventStore\EventQuery;

use DynamicConsistencyBoundary\EventStore\Events\Event;
use DynamicConsistencyBoundary\EventStore\Events\Tags;

final readonly class EventOfTypeWithTags implements Query
{
    public function __construct(
        public string $eventTypeClass,
        public Tags $tags
    )
    {
    }

    public function matches(Event $event): bool
    {
        return $event instanceof $this->eventTypeClass && $event->getTags()->matchesAny($this->tags);
    }
}
