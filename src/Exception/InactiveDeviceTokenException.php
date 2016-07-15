<?php
/**
 * PHP APNS
 *
 * @author Gennady Telegin <gtelegin@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Apns\Exception;

/**
 * Class InactiveDeviceTokenException
 * @package Apns\Exception
 */
class InactiveDeviceTokenException extends ApnsException
{
    /**
     * @var int
     */
    private $timestamp;

    /**
     * InactiveDeviceTokenException constructor.
     *
     * @param string $reason
     * @param int $timestamp
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($reason = '', $timestamp = 0, $code = 0, \Exception $previous = null)
    {
        parent::__construct($reason, $code, $previous);

        $this->timestamp = (int)$timestamp;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
