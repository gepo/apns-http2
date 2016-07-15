<?php

namespace Apns;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $msg = new Message();
        $this->assertEquals(['aps' => []], $msg->getMessageBody());
        $this->assertNull($msg->getDeviceIdentifier());
    }

    public function testCreationWithDeviceIdentifier()
    {
        $msg = new Message('foo');
        $this->assertSame('foo', $msg->getDeviceIdentifier());
    }

    public function testSetTopic()
    {
        $msg = new Message();
        $msg->setTopic('foo');
        $this->assertEquals('foo', $msg->getTopic());
        $this->assertEquals(['apns-topic' => 'foo'], $msg->getMessageHeaders());
    }

    public function testSetPriority()
    {
        $msg = new Message();
        $msg->setPriority(10);
        $this->assertEquals(10, $msg->getPriority());
        $this->assertEquals(['apns-priority' => 10], $msg->getMessageHeaders());
    }

}