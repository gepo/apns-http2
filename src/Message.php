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

/**
 * Class Message.
 *
 * @see https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/TheNotificationPayload.html#//apple_ref/doc/uid/TP40008194-CH107-SW1
 */
class Message
{
    const PRIORITY_IMMEDIATELY = 10;
    const PRIORITY_SOMETIMES = 5;

    /**
     * Custom data for the APS body.
     *
     * @var array
     */
    protected $customData = [];

    /**
     * Device identifier.
     *
     * @var string
     */
    protected $identifier;

    /**
     * The APS core body.
     *
     * @var array
     */
    protected $apsBody = [];

    /**
     * A canonical UUID that identifies the notification.
     *
     * @var string
     */
    protected $id;

    /**
     * Expiration date (UTC).
     *
     * A UNIX epoch date expressed in seconds (UTC).
     * This header identifies the date when the notification is no longer valid and can be discarded.
     * If this value is nonzero, APNs stores the notification and tries to deliver it at least once,
     * repeating the attempt as needed if it is unable to deliver the notification the first time.
     * If the value is 0, APNs treats the notification as if it expires immediately and
     * does not store the notification or attempt to redeliver it.
     *
     * @var int
     */
    protected $expiry = 0;

    /**
     * The priority of the notification. Specify one of the following values:
     *  - 10 – Send the push message immediately. Notifications with this priority must trigger an alert, sound,
     *         or badge on the target device. It is an error to use this priority for a push notification
     *         that contains only the content-available key.
     *  -  5 — Send the push message at a time that takes into account power considerations for the device.
     *         Notifications with this priority might be grouped and delivered in bursts. They are throttled,
     *         and in some cases are not delivered.
     * If you omit this header, the APNs server sets the priority to 10.
     *
     * @var int
     */
    protected $priority;

    /**
     * @var string
     */
    protected $topic;

    /**
     * Class constructor.
     */
    public function __construct($identifier = null)
    {
        $this->apsBody = [
            'aps' => [],
        ];

        if (null !== $identifier) {
            $this->identifier = $identifier;
        }
    }

    /**
     * Gets the full message body to send to APN.
     *
     * @return array
     */
    public function getMessageBody()
    {
        $payloadBody = $this->apsBody;

        if (isset($payloadBody['alert'])
            && $payloadBody['alert'] instanceof MessageAlert
        ) {
            $payloadBody['alert'] = $payloadBody['alert']->getAlertBody();
        }

        if (!empty($this->customData)) {
            $payloadBody = array_merge($payloadBody, $this->customData);
        }

        return $payloadBody;
    }

    /**
     * Gets the apns-* headers to send in requrest to APN.
     *
     * @return array
     */
    public function getMessageHeaders()
    {
        $headers = [];
        if ($this->priority) {
            $headers['apns-priority'] = $this->priority;
        }
        if ($this->topic) {
            $headers['apns-topic'] = $this->topic;
        }

        return $headers;
    }

    /**
     * Sets the alert title. For iOS, this is the APS alert message.
     *
     * @param MessageAlert|string $alert
     *
     * @return self
     *
     * @throws \InvalidArgumentException
     */
    public function setAlert($alert)
    {
        if (!is_string($alert) && !$alert instanceof MessageAlert) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Messages alert must be either string or instance of MessageAlert. Instance of "%s" provided',
                    gettype($alert)
                )
            );
        }

        $this->apsBody['aps']['alert'] = $alert;

        return $this;
    }

    /**
     * Sets any custom data for the APS body.
     *
     * @param array $data
     *
     * @return self
     */
    public function setData(array $data)
    {
        if (array_key_exists('aps', $data)) {
            unset($data['aps']);
        }

        foreach ($data as $key => $value) {
            $this->addCustomData($key, $value);
        }

        return $this;
    }

    /**
     * Add custom data.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function addCustomData($key, $value)
    {
        if ('aps' == $key) {
            throw new \LogicException('Can\'t replace "aps" data. Please call to setMessage, if your want replace message text.');
        }

        if (is_object($value)) {
            if (interface_exists('JsonSerializable')
                && !$value instanceof \stdClass
                && !$value instanceof \JsonSerializable
            ) {
                throw new \InvalidArgumentException(sprintf(
                    'Object %s::%s must be implements JsonSerializable interface for next serialize data.',
                    get_class($value),
                    spl_object_hash($value)
                ));
            }
        }

        $this->customData[$key] = $value;

        return $this;
    }

    /**
     * Sets the identifier of the target device, eg UUID or similar.
     *
     * @param string $identifier
     *
     * @return self
     */
    public function setDeviceIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Returns the device identifier.
     *
     * @return null|string
     */
    public function getDeviceIdentifier()
    {
        return $this->identifier;
    }

    /**
     * iOS-specific
     * Sets the APS sound.
     *
     * @param string $sound The sound to use. Use 'default' to use the built-in default
     *
     * @return self
     */
    public function setAPSSound($sound)
    {
        $this->apsBody['aps']['sound'] = (string) $sound;

        return $this;
    }

    /**
     * iOS-specific
     * Sets the APS badge count.
     *
     * @param int $badge The badge count to display
     *
     * @return self
     */
    public function setAPSBadge($badge)
    {
        $this->apsBody['aps']['badge'] = (int) $badge;

        return $this;
    }

    /**
     * Sets the APS content available flag.
     * This flag means that when your app is launched in the background or resumed,
     * application:didReceiveRemoteNotification:fetchCompletionHandler: is called.
     *
     * @param string $contentAvailable The flag to set the content-available option, only 1 or null.
     *
     * @return self
     */
    public function setAPSContentAvailable($contentAvailable = null)
    {
        if (1 === $contentAvailable) {
            $this->apsBody['aps']['content-available'] = 1;
        } else {
            unset($this->apsBody['aps']['content-available']);
        }

        return $this;
    }

    /**
     * Sets the APS category.
     *
     * @param string $category The notification category
     */
    public function setAPSCategory($category)
    {
        $this->apsBody['aps']['category'] = (string) $category;
    }

    /**
     * Get apns-id of message.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set apns-id of message.
     *
     * @param string $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = (string) $id;

        return $this;
    }

    /**
     * Get expiry of message.
     *
     * @return int
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Set expiry of message.
     *
     * @param int $expiry
     *
     * @return self
     */
    public function setExpiry($expiry)
    {
        $this->expiry = (int) $expiry;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     *
     * @return self
     */
    public function setPriority($priority)
    {
        $this->priority = (int) $priority;

        return $this;
    }

    /**
     * Get apns-topic of message.
     *
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Set apns-topic of message.
     *
     * @param string $topic
     *
     * @return self
     */
    public function setTopic($topic)
    {
        $this->topic = (string) $topic;

        return $this;
    }
}
