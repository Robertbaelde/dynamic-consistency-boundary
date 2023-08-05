<?php

namespace DynamicConsistencyBoundary\EventStore\Tests\Repository;

use DynamicConsistencyBoundary\EventStore\Repository\EventRepositoryInterface;

class InMemoryRepositoryTest extends EventRepositoryTestCase
{
    public function getRepository(): EventRepositoryInterface
    {
        return new InMemoryEventRepository();
    }
}
