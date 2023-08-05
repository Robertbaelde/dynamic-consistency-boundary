<?php

namespace DynamicConsistencyBoundary\EventStore\Events\Serialisation;

use PHPUnit\Event\Event;
use DynamicConsistencyBoundary\EventStore\Events\StorableEvent;

interface EventSerializer
{
    public function serialize(Event $event): StorableEvent;

    public function deserialize(StorableEvent $event): Event;
}
