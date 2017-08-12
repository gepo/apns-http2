<?php
/**
 * PHP APNS.
 *
 * @author Gennady Telegin <gtelegin@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Apns\Handler;

use Apns\Client as ApnsClient;
use Apns\Exception\ApnsException;
use Apns\ExceptionFactory;
use Apns\Message;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;

if (!defined('CURL_HTTP_VERSION_2_0')) {
    define('CURL_HTTP_VERSION_2_0', 3);
}

/**
 * Class GuzzleHandler.
 */
class GuzzleHandler
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * GuzzleHandler constructor.
     */
    public function __construct()
    {
        $this->httpClient = new HttpClient();
    }

    /**
     * @param ApnsClient $apns
     * @param Message    $message
     *
     * @return bool
     *
     * @throws ApnsException
     */
    public function __invoke(ApnsClient $apns, Message $message)
    {
        try {
            $response = $this->httpClient->request(
                'POST',
                $apns->getPushURI($message),
                [
                    'json' => $message,
                    'cert' => $apns->getSslCert(),
                    'headers' => $message->getMessageHeaders(),
                    'curl' => [
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                    ],
                ]
            );

            return 200 === $response->getStatusCode();
        } catch (RequestException $e) {
            throw self::factoryException($e);
        }
    }

    /**
     * @param RequestException $exception
     *
     * @return ApnsException
     */
    private static function factoryException(RequestException $exception)
    {
        $response = $exception->getResponse();

        if (null === $response) {
            return new ApnsException('Unknown network error', 0, $exception);
        }

        try {
            $contents = $response->getBody()->getContents();
        } catch (\Exception $e) {
            return new ApnsException('Unknown network error', 0, $e);
        }

        return ExceptionFactory::factoryException($response->getStatusCode(), $contents, $exception);
    }
}
