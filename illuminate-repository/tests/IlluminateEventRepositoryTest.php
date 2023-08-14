<?php

namespace DynamicConsistencyBoundary\IlluminateEventRepository\Tests;

use DynamicConsistencyBoundary\EventStore\Events\Serialisation\DefaultEventSerializer;
use DynamicConsistencyBoundary\EventStore\Events\Serialisation\DotSeparatedSnakeCaseInflector;
use DynamicConsistencyBoundary\EventStore\Events\Serialisation\UlidEventIdGenerator;
use DynamicConsistencyBoundary\EventStore\EventStore;
use DynamicConsistencyBoundary\EventStore\Repository\EventRepositoryInterface;
use DynamicConsistencyBoundary\EventStore\Tests\Repository\EventRepositoryTestCase;
use DynamicConsistencyBoundary\EventStore\Tests\Repository\InMemoryEventRepository;
use DynamicConsistencyBoundary\IlluminateEventRepository\IlluminateEventRepository;
use Illuminate\Database\Capsule\Manager;

class IlluminateEventRepositoryTest extends EventRepositoryTestCase
{
    private \Illuminate\Database\Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $manager = new Manager();
        $manager->addConnection(
            [
                'driver' => 'mysql',
                'host' => getenv('DCB_TESTING_MYSQL_HOST') ?: '127.0.0.1',
                'port' => getenv('DCB_TESTING_MYSQL_PORT') ?: '3306',
                'database' => 'illuminate_dcb',
                'username' => 'root',
                'password' => 'password',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
            ]
        );

        $this->connection = $manager->getConnection();
        $this->connection->table('events')->truncate();
        $this->connection->table('event_tags')->truncate();
    }

    public function getRepository(): EventRepositoryInterface
    {
        return new IlluminateEventRepository(
            $this->connection,
            new DefaultEventSerializer(
                new UlidEventIdGenerator(),
                new DotSeparatedSnakeCaseInflector(),
            ),
            'events',
            'event_tags',
        );
    }

    /** @test */
    public function test()
    {
        $store = new EventStore(new InMemoryEventRepository());
        $this->assertTrue(true);
    }
}
