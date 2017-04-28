<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Cache;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CacheShim {

    /**
     * @var CacheDecorator
     */
    private $decorator;

    private $disabled = false;

    public function __construct(Shimmable $instance, CacherInterface $cacher, $cacheNamespace="") {
        $this->decorator = new CacheDecorator($instance, $cacher, $cacheNamespace);
    }

    public function callMethod($method, $args) {
        if($this->disabled)
            return false;

        $this->disable();
        $ret = $this->decorator->__call($method, $args);
        $this->enable();
        return $ret;
    }

    public function disable() {
        $this->disabled = true;
    }

    public function enable() {
        $this->disabled = false;
    }

} 