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

/**
 * Class MessageAlert
 *
 * @package Apns
 */
class MessageAlert
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $titleLocKey;

    /**
     * @var string
     */
    protected $titleLocArgs;

    /**
     * @var string
     */
    protected $actionLocKey;

    /**
     * @var string
     */
    protected $locKey;

    /**
     * @var string
     */
    protected $locArgs;

    /**
     * @var string
     */
    protected $launchImage;

    /**
     * @return array
     */
    public function getAlertBody()
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'title-loc-key' => $this->titleLocKey,
            'title-loc-args' => $this->titleLocArgs,
            'action-loc-key' => $this->actionLocKey,
            'loc-key' => $this->locKey,
            'loc-args' => $this->locArgs,
            'laungh-image' => $this->launchImage,
        ];
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitleLocKey()
    {
        return $this->titleLocKey;
    }

    /**
     * @param string $titleLocKey
     * @return self
     */
    public function setTitleLocKey($titleLocKey)
    {
        $this->titleLocKey = $titleLocKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitleLocArgs()
    {
        return $this->titleLocArgs;
    }

    /**
     * @param string $titleLocArgs
     * @return self
     */
    public function setTitleLocArgs($titleLocArgs)
    {
        $this->titleLocArgs = $titleLocArgs;
        return $this;
    }

    /**
     * @return string
     */
    public function getActionLocKey()
    {
        return $this->actionLocKey;
    }

    /**
     * @param string $actionLocKey
     * @return self
     */
    public function setActionLocKey($actionLocKey)
    {
        $this->actionLocKey = $actionLocKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocKey()
    {
        return $this->locKey;
    }

    /**
     * @param string $locKey
     * @return self
     */
    public function setLocKey($locKey)
    {
        $this->locKey = $locKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocArgs()
    {
        return $this->locArgs;
    }

    /**
     * @param string $locArgs
     * @return self
     */
    public function setLocArgs($locArgs)
    {
        $this->locArgs = $locArgs;
        return $this;
    }

    /**
     * @return string
     */
    public function getLaunchImage()
    {
        return $this->launchImage;
    }

    /**
     * @param string $launchImage
     * @return self
     */
    public function setLaunchImage($launchImage)
    {
        $this->launchImage = $launchImage;
        return $this;
    }
}
