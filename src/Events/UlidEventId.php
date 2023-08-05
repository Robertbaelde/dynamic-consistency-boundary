<?php

namespace DynamicConsistencyBoundary\EventStore\Events;

use Symfony\Component\Uid\Ulid as UlidGenerator;

final readonly class UlidEventId implements EventId
{
    public function __construct(
        private string $id
    ) {
    }

    public function toString(): string
    {
        return $this->id;
    }

    public static function fromString(string $id): EventId
    {
        return new static($id);
    }

    public static function generate(): static
    {
        return new static(UlidGenerator::generate());
    }

    public function equals(EventId $that): bool
    {
        return $this->toString() === $that->toString();
    }
}
