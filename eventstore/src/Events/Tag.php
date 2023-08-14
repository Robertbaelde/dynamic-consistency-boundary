<?php

namespace DynamicConsistencyBoundary\EventStore\Events;

final readonly class Tag
{
    public function __construct(
        public string $key,
        public string $value,
    )
    {
    }

    public function equals(Tag $tag): bool
    {
        return $this->key === $tag->key && $this->value === $tag->value;
    }

    public function toPayload(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
        ];
    }

    public static function fromPayload(array $payload): Tag
    {
        return new self(
            key: $payload['key'],
            value: $payload['value'],
        );
    }
}
