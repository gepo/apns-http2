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
use Apns\Exception\CertificateException;
use Apns\Exception\InactiveDeviceTokenException;

/**
 * Class ExceptionFactory.
 */
class ExceptionFactory
{
    /**
     * Factory concrete exception for response from APN service.
     *
     * @param int             $statusCode
     * @param string          $responseContent
     * @param \Exception|null $previous
     *
     * @return ApnsException
     */
    public static function factoryException($statusCode, $responseContent, \Exception $previous = null)
    {
        $data = json_decode($responseContent, true);

        if (!is_array($data)) {
            return new ApnsException(
                sprintf('Unable to parse json `%s`: %s', json_last_error_msg(), $responseContent),
                0,
                $previous
            );
        }

        $reason = isset($data['reason']) ? (string) $data['reason'] : '';

        switch ($statusCode) {
            case 403:
                return new CertificateException($reason, $statusCode, $previous);

            case 410:
                return new InactiveDeviceTokenException(
                    $reason,
                    isset($data['timestamp']) ? $data['timestamp'] : 0,
                    $statusCode,
                    $previous
                );

            default:
                return new ApnsException($reason, $statusCode, $previous);
        }
    }
}
