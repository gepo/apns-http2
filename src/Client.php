<?php
/**
 * PHP APNS
 *
 * @author Gennady Telegin <gtelegin@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Apns;

use Apns\Exception\ApnsException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

if (! defined('CURL_HTTP_VERSION_2_0')) {
    define('CURL_HTTP_VERSION_2_0', 3);
}

/**
 * Class Client
 *
 * @package Apns
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
     * followed by the password required for the certificate in the second element
     *
     * @var array|string
     */
    protected $sslCert;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * AppleNotification constructor.
     *
     * @param string|array $sslCert string containing certificate file name or array [<filename>,<password>]
     * @param bool $useSandbox
     */
    public function __construct($sslCert, $useSandbox = false)
    {
        $this->useSandbox = $useSandbox;
        $this->sslCert = $sslCert;
        $this->httpClient = new HttpClient();
    }

    /**
     * @param Message $message
     * @return bool
     * @throws ApnsException
     */
    public function send(Message $message)
    {
        try {
            $response = $this->httpClient->request(
                'POST',
                sprintf(
                    "%s/3/device/%s",
                    $this->useSandbox ? self::URI_SANDBOX : self::URI_PROD,
                    $message->getDeviceIdentifier()
                ),
                [
                    'json' => $message->getMessageBody(),
                    'cert' => $this->sslCert,
                    'headers' => $message->getMessageHeaders(),
                    'curl' => [
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                    ],
                ]
            );

            return 200 === $response->getStatusCode();
        } catch (RequestException $e) {
            throw ExceptionFactory::createFor($e);
        }
    }
}
