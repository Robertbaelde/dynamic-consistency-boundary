<?php

namespace DynamicConsistencyBoundary\EventStore\Events;

final readonly class StorableEvent
{
    public function __construct(
        public EventId $eventId,
        public string $eventType,
        public string $eventPayload,
        public Tags $tags,
//        public ?EventMetadata $eventMetadata = null,
    )
    {
    }
}
