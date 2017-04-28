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

class APCCacher implements CacherInterface
{
    /**
     * @var string
     */
    private $prefix = '';

    private $last_fetched_key;
    private $last_fetched_value;

    function __construct($prefix = '') {
        $this->prefix = $prefix;
        if(!extension_loaded('apc')) {
            throw new \Exception("APC not loaded");
        }
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
        //deals with apc corruption bug, storing serialized strings, in case it corrupts something, the cache gets regenerated
        //return apc_exists($key);
        return (apc_exists($key) && self::fetch($key)!==FALSE);

    }

    /**
     * @inheritdoc
     */
    function store($key, $value, $ttlsecs=600) {
        return apc_store($key, $value, $ttlsecs);
    }

    /**
     * @inheritdoc
     */
    function fetch($key) {
        if($this->last_fetched_key === $key)
            return $this->last_fetched_value;

        $this->last_fetched_key = $key;
        return $this->last_fetched_value = apc_fetch($key);
    }

    /**
     * @inheritdoc
     */
    function delete($key) {
        return apc_delete($key);
    }

    /**
     * @return boolean
     */
    function flushAll()
    {
        return apc_clear_cache("user");
    }
}
