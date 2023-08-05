<?php

namespace DynamicConsistencyBoundary\EventStore\Tests\Repository;

use DynamicConsistencyBoundary\EventStore\EventStore;
use PHPUnit\Framework\TestCase;
use DynamicConsistencyBoundary\EventStore\EventQuery\EventOfTypeWithTags;
use DynamicConsistencyBoundary\EventStore\EventQuery\EventQuery;
use DynamicConsistencyBoundary\EventStore\Events\Tag;
use DynamicConsistencyBoundary\EventStore\Events\Tags;
use DynamicConsistencyBoundary\EventStore\Repository\EventRepositoryInterface;
use DynamicConsistencyBoundary\EventStore\Tests\Stub\CourseCapacityChanged;
use DynamicConsistencyBoundary\EventStore\Tests\Stub\CourseCapacityGuard;
use DynamicConsistencyBoundary\EventStore\Tests\Stub\StudentSubscribedToCourse;

abstract class EventRepositoryTestCase extends TestCase
{
    /** @test */
    public function it_stores_the_next_events_when_no_new_events_recorded_in_the_meanwhile(): void
    {
        $eventRepository = $this->getRepository();
        $eventStore = new EventStore($eventRepository);
        $courseCapacityGuard = new CourseCapacityGuard('courseA');

        $recorder = $eventStore->prepare($courseCapacityGuard);
        $recorder->record(new StudentSubscribedToCourse('courseA', 'student1'));

        $eventStore->commit($recorder);
        $this->assertCount(1,
            $eventRepository
                ->query(new EventQuery(
                    new EventOfTypeWithTags(
                        StudentSubscribedToCourse::class,
                        new Tags(new Tag('course', 'courseA'), new Tag('student', 'student1')))
                ))
                ->getEvents()
        );
    }

    /** @test */
    public function it_throws_exception_when_course_capacity_changed_in_the_meanwhile()
    {
        $eventRepository = $this->getRepository();
        $eventStore = new EventStore($eventRepository);
        $courseCapacityGuard = new CourseCapacityGuard('courseA');

        $recorder = $eventStore->prepare($courseCapacityGuard);
        $recorder->record(new StudentSubscribedToCourse('courseA', 'student1'));

        $capacityRecorder = $eventStore->prepare();
        $capacityRecorder->record(new CourseCapacityChanged('courseA', 0));
        $eventStore->commit($capacityRecorder);

        // this should fail
        $this->expectException(\Exception::class);
        $eventStore->commit($recorder);
        $this->assertCount(1,
            $eventRepository
                ->query(new EventQuery(
                    new EventOfTypeWithTags(
                        StudentSubscribedToCourse::class,
                        new Tags(new Tag('course', 'courseA'), new Tag('student', 'student1')))
                ))
                ->getEvents()
        );
    }

    /** @test */
    public function it_allows_recording_of_events_that_are_not_part_of_the_query()
    {
        $eventRepository = $this->getRepository();
        $eventStore = new EventStore($eventRepository);
        $courseCapacityGuard = new CourseCapacityGuard('courseA');

        $recorder = $eventStore->prepare($courseCapacityGuard);
        $recorder->record(new StudentSubscribedToCourse('courseA', 'student1'));

        $capacityRecorder = $eventStore->prepare();
        $capacityRecorder->record(new CourseCapacityChanged('courseB', 0));
        $eventStore->commit($capacityRecorder);

        // this should not fail
        $eventStore->commit($recorder);
        $this->assertCount(1,
            $eventRepository
                ->query(new EventQuery(
                    new EventOfTypeWithTags(
                        StudentSubscribedToCourse::class,
                        new Tags(new Tag('course', 'courseA'), new Tag('student', 'student1')))
                ))
                ->getEvents()
        );
    }

    abstract public function getRepository(): EventRepositoryInterface;
}
