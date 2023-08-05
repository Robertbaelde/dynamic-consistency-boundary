<?php

namespace DynamicConsistencyBoundary\EventStore\Events\Serialisation;

use DynamicConsistencyBoundary\EventStore\Events\EventId;
use DynamicConsistencyBoundary\EventStore\Events\UlidEventId;

final readonly class UlidEventIdGenerator implements EventIdGenerator
{
    public function makeNewEventId(): EventId
    {
        return UlidEventId::generate();
    }
}
