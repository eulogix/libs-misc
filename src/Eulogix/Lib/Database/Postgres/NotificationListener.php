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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

use PDO;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Stopwatch\Stopwatch;

class NotificationListener
{
    const EVENT_LISTENING_STARTED = "LISTENING_STARTED";
    const EVENT_NOTIFICATION_RECEIVED = "NOTIFICATION_RECEIVED";
    const EVENT_ERROR = "ERROR";

    /**
     * @var PDO
     */
    private $connection;

    /**
     * @var string[]
     */
    private $channels = [];

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var NotificationHookInterface[]
     */
    private $hooks = [];

    /**
     * @param PDO $connection
     */
    public function __construct(\PDO $connection) {
        $this->connection = $connection;
        $this->dispatcher = new EventDispatcher();

        $this->getDispatcher()->addListener( self::EVENT_NOTIFICATION_RECEIVED,
            function(NotificationEvent $e) {
                foreach($this->hooks as $hook) {
                    try {
                        if($hook->mustExecute($e))
                            $hook->execute($e);
                    } catch(\Throwable $t) {
                        $this->getDispatcher()->dispatch(self::EVENT_ERROR, new GenericEvent($t->getMessage()));
                    }
                }

            }
        );
    }

    /**
     * @param string|string[] $channel
     */
    public function registerChannel($channel) {
        $this->channels = array_unique ( array_merge(is_array($channel) ? $channel : [$channel]) );
    }

    /**
     * @return \string[]
     */
    public function getRegisteredChannels()
    {
        return $this->channels;
    }

    /**
     * @param NotificationHookInterface $hook
     */
    public function addHook(NotificationHookInterface $hook) {
        $this->hooks[] = $hook;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function listen($minutes=null)
    {
        $channels = $this->getRegisteredChannels();
        foreach($channels as $channel)
            $this->connection->exec("LISTEN \"$channel\";");

        $this->dispatcher->dispatch(self::EVENT_LISTENING_STARTED);

        $sw = new Stopwatch();
        $sw->start('loop');

        $notificationCounter = 0;
        while ( !$minutes || ($sw->getEvent('loop')->getDuration()/1000/60 < $minutes) ) {
            if ( $result = $this->connection->pgsqlGetNotify(PDO::FETCH_ASSOC, 1000) ) {

                $this->dispatcher->dispatch(self::EVENT_NOTIFICATION_RECEIVED,
                    new NotificationEvent($result['pid'], $result['message'], $result['payload'], [])
                );

                $notificationCounter++;
            }
        }
    }

}