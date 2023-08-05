<?php

namespace DynamicConsistencyBoundary\EventStore\Events;

interface Event
{
    public function getTags(): Tags;
}
