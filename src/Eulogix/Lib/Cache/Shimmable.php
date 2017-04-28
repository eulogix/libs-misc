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

interface Shimmable {

    /**
     * @return CacheShim
     */
    public function getShim();

    /**
     * @param CacheShim $shim
     */
    public function setShim( $shim );

} 