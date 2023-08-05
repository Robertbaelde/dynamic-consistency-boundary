<?php

namespace DynamicConsistencyBoundary\EventStore\Tests\Stub;

use DynamicConsistencyBoundary\EventStore\Events\Event;
use DynamicConsistencyBoundary\EventStore\Events\Tag;
use DynamicConsistencyBoundary\EventStore\Events\Tags;

final readonly class CourseCapacityChanged implements Event
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
}
