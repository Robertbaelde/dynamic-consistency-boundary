<?php

namespace DynamicConsistencyBoundary\EventStore\Tests\Stub;

use DynamicConsistencyBoundary\EventStore\Events\Event;
use DynamicConsistencyBoundary\EventStore\Events\Serialisation\SerializableEvent;
use DynamicConsistencyBoundary\EventStore\Events\Tag;
use DynamicConsistencyBoundary\EventStore\Events\Tags;

final readonly class StudentSubscribedToCourse implements SerializableEvent
{
    public function __construct(
        public string $courseId,
        public string $studentId,
    )
    {
    }

    public function getTags(): Tags
    {
        return new Tags(
            new Tag('student', $this->studentId),
            new Tag('course', $this->courseId),
        );
    }

    public function toPayload(): array
    {
        return [
            'courseId' => $this->courseId,
            'studentId' => $this->studentId,
        ];
    }

    public static function fromPayload(array $payload): SerializableEvent
    {
        return new self(
            courseId: $payload['courseId'],
            studentId: $payload['studentId'],
        );
    }
}
