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

class CacheDecorator {

    /**
     * @var object
     */
    private $instance;

    /**
     * @var CacherInterface
     */
    private $cacher;

    /**
     * @var array
     */
    private $methods;

    /**
     * @var array
     */
    private $localCache = [];

    /**
     * @var string
     */
    private $cacheNamespace;

    /**
     * @param object $instance
     * @param CacherInterface $cacher
     * @param string $cacheNamespace
     */
    public function __construct($instance, CacherInterface $cacher, $cacheNamespace = "") {
        $this->instance = $instance;
        $this->cacher = $cacher;
        $this->cacheNamespace = $cacheNamespace;
    }

    /**
     * @param string $method
     * @param int $timeToLive
     */
    public function defineCachingForMethod($method, $timeToLive)
    {
        $this->methods[$method] = $timeToLive;
    }

    public function __call($method, $args)
    {
        $cacheToken = $this->getMethodCallToken($method, $args);

        if ($this->hasActiveCacheForMethod($cacheToken)) {
            return $this->getCachedMethodCall($cacheToken);
        } else {
            return $this->cacheAndReturnMethodCall($cacheToken, $method, $args);
        }
    }

    /**
     * @param string $cacheToken
     * @return bool
     */
    private function hasActiveCacheForMethod($cacheToken)
    {
        return isset($this->localCache[$cacheToken]) || $this->cacher->exists( $cacheToken );
    }

    /**
     * @param string $cacheToken
     * @return mixed
     */
    private function getCachedMethodCall($cacheToken)
    {
        return isset($this->localCache[$cacheToken]) ? $this->localCache[$cacheToken] : $this->localCache[$cacheToken] = $this->cacher->fetch( $cacheToken );
    }

    private function cacheAndReturnMethodCall($cacheToken, $method, $args)
    {
        $ret = call_user_func_array([$this->instance, $method], $args);

        $this->cacher->store($cacheToken, $ret);
        $this->localCache[$cacheToken] = $ret;
        return $ret;
    }

    private function getMethodCallToken($method, $args) {
        return $this->cacher->tokenize([$this->cacheNamespace, get_class($this->instance), func_get_args()]);
    }

} 