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
 * dummy implementation that only caches stuff for a single request
 * @author Pietro Baricco <pietro@eulogix.com>
*/

class DummyCacher implements CacherInterface
{
    private $hash;

    /**
     * @inheritdoc
     */
    function tokenize($variable) {
        return md5(serialize($variable));
    }

    /**
     * @inheritdoc
     */
    function exists($key) {
        return isset($this->hash[$key]);
    }

    /**
     * @inheritdoc
     */
    function store($key, $value, $ttlsecs=600) {
        $this->hash[$key]=$value;
        return true;
    }

    /**
     * @inheritdoc
     */
    function fetch($key) {
        return @$this->hash[$key];
    }

    /**
     * @inheritdoc
     */
    function delete($key) {
        unset($this->hash[$key]);
        return true;
    }

    /**
     * @return boolean
     */
    function flushAll()
    {
        $this->hash = [];
    }
}
