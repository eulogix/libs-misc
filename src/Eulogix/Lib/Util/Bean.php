<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Util;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Bean {

    public function __construct($map) {
        $this->_fill($map);
    }

    /**
     * @param array $map
     */
    public function _fill($map) {
        $props = $this->getProperties();
        foreach ($props as $prop) {
            $propName = $prop->getName();
            if(isset($map[$propName]))
                $this->$propName = $map[$propName];
        }
    }

    /**
     * @return array
     */
    public function toArray() {
        $ret = [];
        $props = $this->getProperties();
        foreach ($props as $prop) {
            $propName = $prop->getName();
            $ret[ $propName ] = $this->$propName;
        }
        return $ret;
    }

    /**
     * @return \ReflectionProperty[]
     */
    private function getProperties() {
        $reflect = new \ReflectionClass($this);
        return $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
    }
} 