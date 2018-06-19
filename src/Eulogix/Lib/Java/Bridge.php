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
    private $url = "", $dir = "";

    /**
     * @param string $url
     */
    public function __construct($url, $dir) {
        $this->url = $url;
        $this->dir = $dir;

        $javaInc = $dir.DIRECTORY_SEPARATOR."Java.inc";
        if(!file_exists($javaInc)) {
            copy("$url/java/Java.inc", $javaInc);
        }

        require_once($javaInc);
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