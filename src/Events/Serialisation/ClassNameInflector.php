<?php

namespace DynamicConsistencyBoundary\EventStore\Events\Serialisation;

interface ClassNameInflector
{
    public function classNameToType(string $className): string;

    public function typeToClassName(string $eventType): string;

    public function instanceToType(object $instance): string;
}
