<?php

namespace DynamicConsistencyBoundary\EventStore\Events\Serialisation;

use DynamicConsistencyBoundary\EventStore\Events\Event;
use DynamicConsistencyBoundary\EventStore\Events\StorableEvent;

final readonly class DefaultEventSerializer implements EventSerializer
{
    public function __construct(
        private EventIdGenerator $eventIdGenerator,
        private ClassNameInflector $classNameInflector,
    )
    {
    }

    public function serialize(Event $event): StorableEvent
    {
        if(!$event instanceof SerializableEvent){
            throw new \InvalidArgumentException('Event is not serializable');
        }

        return new StorableEvent(
            eventId: $this->eventIdGenerator->makeNewEventId() ,
            eventType: $this->classNameInflector->instanceToType($event),
            eventPayload: json_encode($event->toPayload()),
            tags: $event->getTags(),
        );
    }

    public function deserialize(StorableEvent $event): Event
    {
        $className = $this->classNameInflector->typeToClassName($event->eventType);
        return $className::fromPayload(json_decode($event->eventPayload, true));
    }
}
