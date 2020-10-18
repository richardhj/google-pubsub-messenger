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

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Bridge\GooglePubSub\Transport\AmazonSqsTransportFactory;
use Symfony\Component\Messenger\Bridge\GooglePubSub\Transport\GooglePubSubTransportFactory;

class GooglePubSubTransportFactoryTest extends TestCase
{
    public function testSupportsOnlySqsTransports()
    {
        $factory = new GooglePubSubTransportFactory();

        $this->assertTrue($factory->supports('gps://localhost', []));
        $this->assertFalse($factory->supports('redis://localhost', []));
        $this->assertFalse($factory->supports('invalid-dsn', []));
    }
}
