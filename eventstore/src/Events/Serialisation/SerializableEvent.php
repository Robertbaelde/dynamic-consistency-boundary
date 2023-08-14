<?php

namespace DynamicConsistencyBoundary\EventStore\Events\Serialisation;

use DynamicConsistencyBoundary\EventStore\Events\Event;

interface SerializableEvent extends Event
{
    public function toPayload(): array;

    public static function fromPayload(array $payload): SerializableEvent;
}
