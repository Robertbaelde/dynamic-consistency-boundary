<?php

namespace DynamicConsistencyBoundary\EventStore\Events;

interface EventId
{
    public function toString(): string;

    public static function fromString(string $id): EventId;

    public static function generate(): static;

    public function equals(self $that): bool;
}
