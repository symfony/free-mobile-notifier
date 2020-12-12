<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Notifier\Bridge\FreeMobile\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\Bridge\FreeMobile\FreeMobileTransport;
use Symfony\Component\Notifier\Exception\UnsupportedMessageTypeException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FreeMobileTransportTest extends TestCase
{
    public function testToStringContainsProperties()
    {
        $transport = $this->getTransport('0611223344');

        $this->assertSame('freemobile://host.test?phone=0611223344', (string) $transport);
    }

    public function testSupportsMessageInterface()
    {
        $transport = $this->getTransport('0611223344');

        $this->assertTrue($transport->supports(new SmsMessage('0611223344', 'Hello!')));
        $this->assertTrue($transport->supports(new SmsMessage('+33611223344', 'Hello!')));
        $this->assertFalse($transport->supports(new SmsMessage('0699887766', 'Hello!')));
        $this->assertFalse($transport->supports($this->createMock(MessageInterface::class)));

        $transport = $this->getTransport('+33611223344');

        $this->assertTrue($transport->supports(new SmsMessage('0611223344', 'Hello!')));
        $this->assertTrue($transport->supports(new SmsMessage('+33611223344', 'Hello!')));
    }

    public function testSendNonSmsMessageThrowsException()
    {
        $transport = $this->getTransport('0611223344');

        $this->expectException(UnsupportedMessageTypeException::class);

        $transport->send($this->createMock(MessageInterface::class));
    }

    public function testSendSmsMessageButInvalidPhoneThrowsException()
    {
        $transport = $this->getTransport('0611223344');

        $this->expectException(UnsupportedMessageTypeException::class);

        $transport->send(new SmsMessage('0699887766', 'Hello!'));
    }

    private function getTransport(string $phone): FreeMobileTransport
    {
        return (new FreeMobileTransport('login', 'pass', $phone, $this->createMock(HttpClientInterface::class)))->setHost('host.test');
    }
}
