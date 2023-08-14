<?php

use EventSauce\BackOff\LinearBackOffStrategy;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

/**
 * @codeCoverageIgnore
 */
include __DIR__ . '/vendor/autoload.php';

function setup_database(string $driver, string $host, string $port): void
{
    $manager = new Manager;
    $manager->addConnection(
        [
            'driver' => $driver,
            'host' => $host,
            'port' => $port,
            'database' => 'illuminate_dcb',
            'username' => 'root',
            'password' => 'password',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ]
    );

    $tries = 0;
    $backOff = new LinearBackOffStrategy(200000, 50);

    while (true) {
        start:
        try {
            $tries++;
            $connection = $manager->getConnection();
            $connection->select('SELECT 1');
            fwrite(STDOUT, "DB connection established!\n");
            break;
        } catch (Throwable $exception) {
            fwrite(STDOUT, "Waiting for a DB connection...\n" . $exception->getMessage());
            $backOff->backOff($tries, $exception);
            goto start;
        }
    }

    $schema = $connection->getSchemaBuilder();
    $schema->dropIfExists('events');
    $schema->create('events', function(Blueprint $table) {
        $table->id('index');
        $table->ulid('id')->unique();
        $table->string('type');
        $table->json('payload');
        $table->json('tags');
    });

    $schema->dropIfExists('event_tags');
    $schema->create('event_tags', function(Blueprint $table) {
        $table->ulid('event_id');
        $table->string('key');
        $table->string('value');
        $table->primary(['event_id', 'key', 'value'], 'event_tags_primary');
    });
}

setup_database(
    'mysql',
    getenv('DCB_TESTING_MYSQL_HOST') ?: '127.0.0.1',
    getenv('DCB_TESTING_MYSQL_PORT') ?: '3306',
);
