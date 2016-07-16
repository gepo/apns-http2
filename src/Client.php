<?php
/**
 * PHP APNS.
 *
 * @author Gennady Telegin <gtelegin@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Apns;

use Apns\Exception\ApnsException;
use Apns\Handler\HandlerFactory;

/**
 * Class Client.
 */
class Client
{
    const URI_SANDBOX = 'https://api.development.push.apple.com:443';
    const URI_PROD = 'https://api.push.apple.com:443';

    /**
     * @var bool
     */
    protected $useSandbox;

    /**
     * Path to a file containing a private SSL key in PEM format.
     * If a password is required, then it's an array containing the path to the SSL key in the first array element
     * followed by the password required for the certificate in the second element.
     *
     * @var array|string
     */
    protected $sslCert;

    /**
     * @var callable
     */
    private $handler;

    /**
     * AppleNotification constructor.
     *
     * @param string|array $sslCert    string containing certificate file name or array [<filename>,<password>]
     * @param bool         $useSandbox
     * @param callable     $handler
     */
    public function __construct($sslCert, $useSandbox = false, callable $handler = null)
    {
        $this->useSandbox = $useSandbox;
        $this->sslCert = $sslCert;

        $this->handler = $handler ?: HandlerFactory::create();
    }

    /**
     * @param Message $message
     *
     * @return bool
     *
     * @throws ApnsException
     */
    public function send(Message $message)
    {
        $handler = $this->handler;

        return $handler($this, $message);
    }

    /**
     * @return array|string
     */
    public function getSslCert()
    {
        return $this->sslCert;
    }

    /**
     * @return string
     */
    public function getServer()
    {
        return $this->useSandbox ? self::URI_SANDBOX : self::URI_PROD;
    }

    /**
     * @param Message $message
     *
     * @return string
     */
    public function getPushURI(Message $message)
    {
        return sprintf('%s/3/device/%s', $this->getServer(), $message->getDeviceIdentifier());
    }
}
