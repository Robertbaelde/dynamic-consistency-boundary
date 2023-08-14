<?php

namespace DynamicConsistencyBoundary\EventStore\Events;

final readonly class Tags
{
    /**
     * @var Tag[]
     */
    private array $tags;

    public function __construct(Tag ...$tags)
    {
        $this->tags = $tags;
    }

    public function matchesExact(Tags $tags): bool
    {
        if(count($this->tags) !== count($tags->tags)){
            return false;
        }
        foreach($this->tags as $tag){
            if(!$tags->hasTag($tag)){
                return false;
            }
        }
        return true;
    }

    private function hasTag(Tag $tag): bool
    {
        foreach($this->tags as $t){
            if($t->equals($tag)){
                return true;
            }
        }
        return false;
    }

    public function addTag(Tag $tag): self
    {
        if($this->hasTag($tag)){
            return $this;
        }

        $tags = $this->tags;
        $tags[] = $tag;
        return new self(...$tags);
    }

    public function matchesAny(Tags $tags): bool
    {
        foreach($tags->tags as $tag){
            if($this->hasTag($tag)){
                return true;
            }
        }
        return false;
    }

    public function matchesAll(Tags $tags): bool
    {
        foreach($tags->tags as $tag){
            if(!$this->hasTag($tag)){
                return false;
            }
        }
        return true;
    }

    public function toPayload(): array
    {
        return array_map(fn(Tag $tag) => $tag->toPayload(), $this->tags);
    }

    public static function fromPayload(array $payload): self
    {
        return new self(...array_map(fn(array $tag) => Tag::fromPayload($tag), $payload));
    }

    /**
     * @return Tag[]
     */
    public function all(): array
    {
        return $this->tags;
    }

}
