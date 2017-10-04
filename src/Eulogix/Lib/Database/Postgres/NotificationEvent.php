<?php
/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Database\Postgres;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class NotificationEvent extends Event
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var int
     */
    protected $pid;

    /**
     * @var string
     */
    protected $channel, $payload;

    /**
     * @param int $pid
     * @param string $channel
     * @param string $payload
     * @param array $eventAttributes
     */
    public function __construct($pid, $channel, $payload, array $eventAttributes = [])
    {
        $this->pid = $pid;
        $this->channel = $channel;
        $this->payload = $payload;
        $this->attributes = $eventAttributes;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}