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

use Google\Cloud\PubSub\PubSubClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Bridge\GooglePubSub\Transport\Connection;

class ConnectionTest extends TestCase
{
    public function testExtraOptions()
    {
        $this->expectException(\InvalidArgumentException::class);
        Connection::fromDsn('gps://default/queue', [
            'extra_key',
        ]);
    }

    public function testExtraParamsInQuery()
    {
        $this->expectException(\InvalidArgumentException::class);
        Connection::fromDsn('gps://default/queue?extra_param=some_value');
    }

    public function testConfigure()
    {
        $projectId = 'my-awesome-project';
        $this->assertEquals(
            new Connection(['topic'=>'my-topic'], new PubSubClient(['projectId' => $projectId])),
            Connection::fromDsn('gps://default/my-awesome-project', [
                'topic' => 'my-topic',
            ])
        );
    }

    public function testFromInvalidDsn()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The given Google Pub/Sub DSN "gps://" is invalid.');

        Connection::fromDsn('gps://');
    }

    public function testFromDsn()
    {
        $this->assertEquals(
            new Connection(['topic' => 'my-topic-1'], new PubSubClient(['projectId' => 'my-awesome-project'])),
            Connection::fromDsn('gps://default/my-awesome-project?topic=my-topic-1')
        );
    }

    public function testFromDsnWithCustomEndpoint()
    {
        $this->assertEquals(
            new Connection(['topic' => 'my-topic-1'], new PubSubClient(['apiEndpoint' => 'localhost:8000', 'projectId' => 'my-awesome-project'])),
            Connection::fromDsn('gps://localhost:8000/my-awesome-project?topic=my-topic-1')
        );
    }

    public function testFromDsnWithInvalidQueryString()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown option found in DSN: [foo]. Allowed options are [apiEndpoint, projectId, keyFile, keyFilePath, requestTimeout, retries, scopes, quotaProject, auto_setup, topic, subscription].');

        Connection::fromDsn('gps://default?foo=foo');
    }

    public function testFromDsnWithInvalidOption()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown option found: [bar]. Allowed options are [apiEndpoint, projectId, keyFile, keyFilePath, requestTimeout, retries, scopes, quotaProject, auto_setup, topic, subscription].');

        Connection::fromDsn('gps://default', ['bar' => 'bar']);
    }

    public function testFromDsnWithInvalidQueryStringAndOption()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown option found: [bar]. Allowed options are [apiEndpoint, projectId, keyFile, keyFilePath, requestTimeout, retries, scopes, quotaProject, auto_setup, topic, subscription].');

        Connection::fromDsn('gps://default?foo=foo', ['bar' => 'bar']);
    }
}
