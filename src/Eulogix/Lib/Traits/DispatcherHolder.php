<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Lib\Traits;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

trait DispatcherHolder {

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;


    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher ?? ($this->dispatcher = new EventDispatcher());
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     * @return $this
     */
    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

}