<?php

namespace DynamicConsistencyBoundary\EventStore\Tests\Stub;

use DynamicConsistencyBoundary\EventStore\Events\Event;
use DynamicConsistencyBoundary\EventStore\Events\Tag;
use DynamicConsistencyBoundary\EventStore\Events\Tags;

final readonly class StudentSubscribedToCourse implements Event
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
}
