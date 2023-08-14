<?php

namespace DynamicConsistencyBoundary\EventStore\Tests\Stub;

use DynamicConsistencyBoundary\EventStore\EventQuery\EventOfTypeWithTags;
use DynamicConsistencyBoundary\EventStore\EventQuery\EventQuery;
use DynamicConsistencyBoundary\EventStore\Events\Event;
use DynamicConsistencyBoundary\EventStore\Events\Tag;
use DynamicConsistencyBoundary\EventStore\Events\Tags;
use DynamicConsistencyBoundary\EventStore\Guards\Guard;

class CourseCapacityGuard implements Guard
{
    public int $subscribedStudentCount = 0;
    private int $capacity = 0;

    public function __construct(public string $courseId)
    {
    }

    public function getEventQuery(): EventQuery
    {
        return new EventQuery(
            new EventOfTypeWithTags(StudentSubscribedToCourse::class, new Tags(new Tag(
                'course', $this->courseId
                ))),
                new EventOfTypeWithTags(CourseCapacityChanged::class, new Tags(new Tag(
                    'course', $this->courseId
                ))),
        );
    }

    public function apply(Event $event): void
    {
        match ($event::class) {
            StudentSubscribedToCourse::class => $this->applyStudentSubscribedToCourse($event),
            CourseCapacityChanged::class => $this->applyCourseCapacityChanged($event),
            default => null,
        };
    }

    private function applyStudentSubscribedToCourse(StudentSubscribedToCourse $event): void
    {
        $this->subscribedStudentCount++;
    }

    private function applyCourseCapacityChanged(CourseCapacityChanged $event): void
    {
        $this->capacity = $event->capacity;
    }
}
