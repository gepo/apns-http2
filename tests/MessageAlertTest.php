<?php

namespace Apns;

class MessageAlertTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataSettersAndGetters
     */
    public function testSettersAndGetters($method, $jsonKey)
    {
        $alert = new MessageAlert();
        $alert->{'set'.$method}('foo');

        $this->assertSame('foo', $alert->{'get'.$method}());
        $this->assertSame([$jsonKey => 'foo'], $this->serialized($alert));
    }

    /**
     * @return array
     */
    public function dataSettersAndGetters()
    {
        return [
            ['Title', 'title'],
            ['Body', 'body'],
            ['TitleLocKey', 'title-loc-key'],
            ['TitleLocArgs', 'title-loc-args'],
            ['ActionLocKey', 'action-loc-key'],
            ['LocKey', 'loc-key'],
            ['LocArgs', 'loc-args'],
            ['LaunchImage', 'laungh-image'],
        ];
    }

    /**
     * @param MessageAlert $msg
     * @return array
     */
    private function serialized(MessageAlert $msg)
    {
        return json_decode(json_encode($msg), true);
    }
}