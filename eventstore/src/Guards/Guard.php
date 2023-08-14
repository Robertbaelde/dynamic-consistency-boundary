<?php

namespace DynamicConsistencyBoundary\EventStore\Guards;

use DynamicConsistencyBoundary\EventStore\EventQuery\EventQuery;
use DynamicConsistencyBoundary\EventStore\Events\Event;

interface Guard
{
    public function getEventQuery(): EventQuery;

    public function apply(Event $event): void;
}
