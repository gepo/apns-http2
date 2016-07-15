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
use Apns\Exception\CertificateException;
use Apns\Exception\InactiveDeviceTokenException;
use GuzzleHttp\Exception\RequestException;

/**
 * Class ExceptionFactory
 *
 * @package Apns
 */
class ExceptionFactory
{
    /**
     * @param RequestException $exception
     * @return ApnsException
     */
    public static function createFor(RequestException $exception)
    {
        $response = $exception->getResponse();

        if (null === $response) {
            return new ApnsException(
                'Unknown network error',
                0,
                $exception
            );
        }

        try {
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return new ApnsException(
                'Unknown network error',
                0,
                $e
            );
        }

        $reason = isset($data['reason']) ? (string)$data['reason'] : '';

        switch ($response->getStatusCode()) {
            case 403:
                return new CertificateException(
                    $reason,
                    $response->getStatusCode(),
                    $exception
                );

            case 410:
                return new InactiveDeviceTokenException(
                    $reason,
                    isset($data['timestamp']) ? $data['timestamp'] : 0,
                    $response->getStatusCode(),
                    $exception
                );

            default:
                return new ApnsException(
                    $reason,
                    $response->getStatusCode(),
                    $exception
                );
        }
    }
}
