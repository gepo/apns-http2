<?php

namespace Apns;

use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
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

    public function testSetId()
    {
        $msg = new Message();
        $msg->setId('foo');
        $this->assertEquals('foo', $msg->getId());
        $this->assertEquals(['apns-id' => 'foo'], $msg->getMessageHeaders());
    }

    public function testSetExpiration()
    {
        $msg = new Message();
        $msg->setExpiry(999);
        $this->assertEquals(999, $msg->getExpiry());
        $this->assertEquals(['apns-expiration' => 999], $msg->getMessageHeaders());
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

    public function testSetAPSSound()
    {
        $msg = new Message();
        $msg->setAPSSound('foo');
        $this->assertEquals(['aps' => ['sound' => 'foo']], $this->serialized($msg));
    }

    public function testSetAPSBadge()
    {
        $msg = new Message();
        $msg->setAPSBadge(999);
        $this->assertEquals(['aps' => ['badge' => 999]], $this->serialized($msg));
    }

    public function testSetAPSContentAvailable()
    {
        $msg = new Message();
        $msg->setAPSContentAvailable(1);
        $this->assertEquals(['aps' => ['content-available' => 1]], $this->serialized($msg));

        $msg->setAPSContentAvailable();
        $this->assertEquals(['aps' => []], $this->serialized($msg));
    }

    public function testSetCategory()
    {
        $msg = new Message();
        $msg->setAPSCategory('foo');

        $this->assertEquals(['aps' => ['category' => 'foo']], $this->serialized($msg));
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

    public function testSetInvalidAlert()
    {
        $this->expectException(\Exception::class);
        $msg = new Message();
        $msg->setAlert(new \stdClass());
    }

    public function testSetData()
    {
        $msg = new Message();
        $msg->setData(['aps' => 'foo', 'foo' => 'bar']);

        $this->assertEquals(['aps' => [], 'foo' => 'bar'], $this->serialized($msg));
    }

    public function testSetCustomData()
    {
        $msg = new Message();
        $msg->addCustomData('foo', 'bar');

        $this->assertEquals(['aps' => [], 'foo' => 'bar'], $this->serialized($msg));
    }

    public function testSetCustomDataInvalidKey()
    {
        $this->expectException(\Exception::class);
        $msg = new Message();
        $msg->addCustomData('aps', 'foo');
    }

    public function testSetCustomDataInvalidObject()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('DateTime');
        $msg = new Message();
        $msg->addCustomData('foo', new \DateTime);
    }

    public function testSetCustomDataValidObject()
    {
        $msg = new Message();
        $msg->addCustomData('foo', new \stdClass());

        $this->assertEquals(['aps' => [], 'foo' => []], $this->serialized($msg));
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
