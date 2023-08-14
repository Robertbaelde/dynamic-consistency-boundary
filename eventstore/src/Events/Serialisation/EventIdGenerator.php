<?php

namespace DynamicConsistencyBoundary\EventStore\Events\Serialisation;

use DynamicConsistencyBoundary\EventStore\Events\EventId;

interface EventIdGenerator
{
    public function makeNewEventId(): EventId;
}
