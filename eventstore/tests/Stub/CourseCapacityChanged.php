<?php

namespace DynamicConsistencyBoundary\EventStore\Tests\Stub;

use DynamicConsistencyBoundary\EventStore\Events\Event;
use DynamicConsistencyBoundary\EventStore\Events\Serialisation\SerializableEvent;
use DynamicConsistencyBoundary\EventStore\Events\Tag;
use DynamicConsistencyBoundary\EventStore\Events\Tags;

final readonly class CourseCapacityChanged implements SerializableEvent
{
    public function __construct(
        public string $courseId,
        public int $capacity,
    )
    {
    }

    public function getTags(): Tags
    {
        return new Tags(
            new Tag('course', $this->courseId),
        );
    }

    public function toPayload(): array
    {
        return [
            'courseId' => $this->courseId,
            'capacity' => $this->capacity,
        ];
    }

    public static function fromPayload(array $payload): SerializableEvent
    {
        return new self(
            courseId: $payload['courseId'],
            capacity: $payload['capacity'],
        );
    }
}
