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

interface CacherInterface
{
    /**
     * returns the unique identifier for the given resource
     * @param mixed $variable
     * @return string
     */
    function tokenize($variable);

    /**
     * @param mixed $key
     * @return boolean
     */
    function exists($key);

    /**
     * @param mixed $key
     * @return boolean
     */
    function delete($key);

    /**
     * @param mixed $key
     * @param mixed $value
     * @param int $ttlsecs
     * @return boolean
     */
    function store($key, $value, $ttlsecs = 600);

    /**
     * @param $key
     * @return mixed
     */
    function fetch($key);

    /**
     * @return boolean
     */
    function flushAll();
}