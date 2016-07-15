<?php

namespace Apns;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateSandbox()
    {
        $client = new Client('', true);
        $this->assertContains('development', $client->getServer());
    }

    public function testCreateProd()
    {
        $client = new Client('', false);
        $this->assertNotContains('development', $client->getServer());
    }

    public function testCreatePushURI()
    {
        $client = new Client('', true);
        $pushUri = $client->getPushURI((new Message())->setDeviceIdentifier('foobar'));

        $this->assertContains('development', $pushUri);
        $this->assertContains('foobar', $pushUri);
    }
}