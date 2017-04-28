<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Graph;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DisjointSets
{
    /**
     * @var array
     */
    var $sets = [];

    function makeSet($x)
    {
        if (!$this->findSet($x))
            $this->sets[] =
                [ $x ];
    }

    function findSet($x)
    {
        foreach($this->sets as $key => $set) {
            $found = array_search($x, $set);
            if ($found !== false) return $key;
        }
        return false;
    }

    function union($x, $y)
    {
        $key_x = $this->findSet($x);
        $key_y = $this->findSet($y);
        if ($key_x == $key_y || $key_x === false || $key_y == false) return false;
        $this->sets[$key_x] = array_merge($this->sets[$key_x], $this->sets[$key_y]);
        unset($this->sets[$key_y]);
    }

    function getSets()
    {
        return $this->sets;
    }
}