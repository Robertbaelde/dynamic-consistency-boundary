<?php

namespace DynamicConsistencyBoundary\IlluminateEventRepository;

use DynamicConsistencyBoundary\EventStore\EventQuery\EventOfTypeWithAllTags;
use DynamicConsistencyBoundary\EventStore\EventQuery\EventOfTypeWithTags;
use DynamicConsistencyBoundary\EventStore\EventQuery\EventQuery;
use DynamicConsistencyBoundary\EventStore\EventRecorder;
use DynamicConsistencyBoundary\EventStore\Events\Event;
use DynamicConsistencyBoundary\EventStore\Events\EventQueryResult;
use DynamicConsistencyBoundary\EventStore\Events\Serialisation\EventSerializer;
use DynamicConsistencyBoundary\EventStore\Events\StorableEvent;
use DynamicConsistencyBoundary\EventStore\Events\Tags;
use DynamicConsistencyBoundary\EventStore\Events\UlidEventId;
use DynamicConsistencyBoundary\EventStore\Repository\EventRepositoryInterface;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;

final class IlluminateEventRepository implements EventRepositoryInterface
{
    public function __construct(
        private ConnectionInterface $connection,
        private EventSerializer $eventSerializer,
        private string $tableName = 'events',
        private string $tagsTableName = 'event_tags',
    )
    {
    }

    public function query(EventQuery $eventQuery): EventQueryResult
    {
//        $query = $this->connection->table($this->tableName)
//            ->where(function (Builder $query) {
//                $query->select('event_id')
//                    ->from($this->tagsTableName)
//                    ->where('key', 'course')
//                    ->where('value', 'courseA')
//                    ->get();
//            }, 'id')
//            ->toRawSql();




//            ->where('event_tags.key', 'course')
//            ->where('event_tags.value', 'courseA')
//            ->select('events.*')
//            ->get();

        $query = $this->connection->table($this->tableName)
            ->join('event_tags', 'events.id', '=', 'event_tags.event_id')
            ->where('id', 'a'); // so we can use orWhere

        foreach ($eventQuery->queries as $q){
            if($q instanceof EventOfTypeWithAllTags){
                $query->orWhere(function (Builder $query) use ($q) {
                    foreach ($q->tags->all() as $i => $tag){
                        $query->where('event_tags.key', $tag->key)
                            ->where('event_tags.value', $tag->value);
                    }
                });
//                $query->tags->all()->forEach(function (string $tag) use ($query, $query){
//                    $query->whereRaw("JSON_CONTAINS(tags, JSON_QUOTE(?))", [$tag]);
//                });
            }
        }
        $query->select('events.*')->distinct();
        print_r($query->toRawSql());
        print_r($query->select('events.*')->distinct()->get());
        return new EventQueryResult(
            new IlluminateOptimisticLock(),
            ...$this->connection->table($this->tableName)
                ->get()->map(function (object $event){
                    return $this->eventSerializer->deserialize(new StorableEvent(
                    // todo, make generic
                        eventId: UlidEventId::fromString($event->id),
                        eventType: $event->type,
                        eventPayload: $event->payload,
                        tags: Tags::fromPayload(json_decode($event->tags, true))
                    ));
                })->toArray()
        );
    }

    public function commitWithoutGuard(Event ...$events): void
    {
        $storableEvents = array_map(fn(Event $event) => $this->eventSerializer->serialize($event), $events);
        $eventsInsert = array_map(fn(StorableEvent $event) => [
            'id' => $event->eventId->toString(),
            'type' => $event->eventType,
            'payload' => $event->eventPayload,
            'tags' => json_encode($event->tags->toPayload()),
        ], $storableEvents);

        $tagsInsert = [];
        foreach ($storableEvents as $storableEvent){
            foreach ($storableEvent->tags->all() as $tag){
                $tagsInsert[] = [
                    'event_id' => $storableEvent->eventId->toString(),
                    'key' => $tag->key,
                    'value' => $tag->value,
                ];
            }
        }

        $this->connection->transaction(function () use ($eventsInsert, $tagsInsert) {
            $this->connection->table($this->tableName)->insert($eventsInsert);
            $this->connection->table($this->tagsTableName)->insert($tagsInsert);
        });

    }

    public function commit(EventRecorder $eventRecorder): void
    {
        // do query in parallel?
    }
}
