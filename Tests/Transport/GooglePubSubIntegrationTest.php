<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Messenger\Bridge\GooglePubSub\Tests\Transport;

use AsyncAws\Sqs\SqsClient;
use Google\Cloud\PubSub\PubSubClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Bridge\GooglePubSub\Tests\Fixtures\DummyMessage;
use Symfony\Component\Messenger\Bridge\GooglePubSub\Transport\Connection;

/**
 * @group integration
 */
class GooglePubSubIntegrationTest extends TestCase
{
    public function testConnectionSendAndGet(): void
    {
        if (!getenv('MESSENGER_GPS_DSN')) {
            $this->markTestSkipped('The "MESSENGER_GPS_DSN" environment variable is required.');
        }

        $this->execute(getenv('MESSENGER_GPS_DSN'));
    }

    private function execute(string $dsn): void
    {
        $connection = Connection::fromDsn($dsn, []);

        $connection->send('{"message": "Hi"}', ['type' => DummyMessage::class, DummyMessage::class => 'special']);
        $this->assertSame(1, $connection->getMessageCount());

        $wait = 0;
        while ((null === $encoded = $connection->get()) && $wait++ < 200) {
            usleep(5000);
        }

        $this->assertEquals('{"message": "Hi"}', $encoded['body']);
        $this->assertEquals(['type' => DummyMessage::class, DummyMessage::class => 'special'], $encoded['headers']);
    }
}
