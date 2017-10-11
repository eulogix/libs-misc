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

class MemcachedCacher implements CacherInterface
{
    /**
     * @var string
     */
    private $prefix = '';

    /**
     * @var \Memcached
     */
    private $memcached;

    /**
     * used to avoid double hits to memcached server when checking if a key exists, gets purged very frequently
     * @var array
     */
    private $localCache = [];

    const MAX_LOCAL_CACHE_SIZE = 10;

    /**
     * @param string $server
     * @param string $port
     * @param string $prefix
     */
    function __construct($server, $port, $prefix = '') {
        $this->prefix = $prefix;

        $this->memcached = new \Memcached();
        $this->memcached->addServer($server, $port);
    }

    /**
     * @inheritdoc
     */
    function tokenize($variable) {
        return $this->prefix.md5(json_encode($variable));
    }

    /**
     * @inheritdoc
     */
    function exists($key) {
        $this->purgeLocalCache();
        $item = $this->memcached->get($key);
        if( $this->memcached->getResultCode() == \Memcached::RES_SUCCESS) {
            $this->localCache[ $key ] = $item;
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    function store($key, $value, $ttlsecs = 600) {
        return $this->memcached->set($key, $value, $ttlsecs);
    }

    /**
     * @inheritdoc
     */
    function fetch($key) {
        $this->purgeLocalCache();
        if(isset($this->localCache[ $key ]))
            return $this->localCache[ $key ];

        $item = $this->memcached->get($key);
        if( $this->memcached->getResultCode() == \Memcached::RES_SUCCESS) {
            $this->localCache[ $key ] = $item;
            return $item;
        }
        
        return false;
    }

    /**
     * @inheritdoc
     */
    function delete($key) {
        $this->purgeLocalCache();
        return $this->memcached->delete($key);
    }

    /**
     * @return boolean
     */
    function flushAll()
    {
        $this->memcached->flush();
        $this->purgeLocalCache();
    }

    private function purgeLocalCache() {
        if(count($this->localCache) >= self::MAX_LOCAL_CACHE_SIZE)
            $this->localCache = [];
    }
}
