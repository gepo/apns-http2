<?php

namespace Apns;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $msg = new Message();
        $this->assertEquals(['aps' => []], $this->serialized($msg));
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

    public function testSetAlert()
    {
        $msg = new Message();
        $msg->setAlert('foo');
        $this->assertEquals(['aps' => ['alert' => 'foo']], $this->serialized($msg));
    }

    public function testSetCustomAlert()
    {
        $msg = new Message();
        $msg->setAlert(
            (new MessageAlert())
                ->setBody('foo')
        );

        $this->assertEquals(['aps' => ['alert' => ['body' => 'foo']]], $this->serialized($msg));
    }

    public function testSetCustomData()
    {
        $msg = new Message();
        $msg->addCustomData('foo', 'bar');

        $this->assertEquals(['aps' => [], 'foo' => 'bar'], $this->serialized($msg));
    }

    /**
     * @param Message $msg
     * @return array
     */
    private function serialized(Message $msg)
    {
        return json_decode(json_encode($msg), true);
    }
}