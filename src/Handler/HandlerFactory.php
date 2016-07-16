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

/**
 * Class HandlerFactory.
 */
class HandlerFactory
{
    /**
     * Choose preferable handler according available libraries and their capabilities.
     *
     * @return callable
     *
     * @throws \LogicException
     */
    public static function create()
    {
        if (class_exists('\GuzzleHttp\Client')) {
            return new GuzzleHandler();
        }

        throw new \LogicException(
            'PHP APNS library requires one of network libraries be installed: %s',
            implode(',', ['guzzlehttp/guzzle'])
        );
    }
}
