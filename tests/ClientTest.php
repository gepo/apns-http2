<?php

namespace Apns;

use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testCreateSandbox()
    {
        $client = new Client('', true);
        $this->assertStringContainsString('development', $client->getServer());
    }

    /**
     * @dataProvider dataValidSslCert
     */
    public function testSslCert($sslCert)
    {
        $client = new Client($sslCert, true);
        $this->assertSame($sslCert, $client->getSslCert());
    }

    public function dataValidSslCert()
    {
        return [
            ['foo.pem'],
            [['foo.pem', 'bar']],
        ];
    }

    public function testCreateProd()
    {
        $client = new Client('', false);
        $this->assertStringNotContainsString('develop', $client->getServer());
    }

    public function testCreatePushURI()
    {
        $client = new Client('', true);
        $pushUri = $client->getPushURI((new Message())->setDeviceIdentifier('foobar'));

        $this->assertStringContainsString('develop', $pushUri);
        $this->assertStringContainsString('foobar', $pushUri);
    }

    public function testSend()
    {
        $mock = $this->getMockCallable();
        $client = new Client('', true, $mock);
        $message = new Message();

        $mock->expects($this->once())->method('__invoke')->with($client, $message);

        $client->send($message);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockCallable()
    {
        return $this->getMockBuilder('stdClass')->setMethods(['__invoke'])->getMock();
    }
}
