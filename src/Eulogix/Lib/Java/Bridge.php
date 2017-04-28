<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Java;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Bridge {

    /**
     * @var string
     */
    private $url = "";

    /**
     * @param string $url
     */
    public function __construct($url) {
        $this->url = $url;
        require_once("$url/java/Java.inc");
    }

    /**
     * @param string $class
     * @return object
     */
    public function instanceJavaClass($class)
    {
        return new \java($class);
    }

} 