<?php
/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\HTTP;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class HTTPUtils
{
    /**
     * @param array $arrays,...
     * @return string
     */
    public static function getPostDataFromAssociativeArrays($arrays)
    {
        $ret = [];
        $arrays = func_get_args();
        foreach($arrays as $array)
            foreach($array as $key => $value)
                $ret[$key] = urlencode($key).'='.urlencode($value);
        return implode('&', $ret);
    }
}