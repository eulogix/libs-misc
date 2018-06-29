<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Database\Postgres;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class PgUtils {

    /**
     * @param $pgArray string
     * @return array
     */
    public static function fromPGArray($pgArray) {
        $tmp = explode(',', preg_replace('/[{}]/sim','', $pgArray) );
        $ret = [];
        foreach($tmp as $v)
            if($v != 'NULL' && $v != "")
                $ret[] = $v;
        return $ret;
    }

    /**
     * @param $PHPArray array
     * @return string
     */
    public static function toPGArray($PHPArray)
    {
        return is_array($PHPArray) && count($PHPArray) > 0 ? '{'.implode($PHPArray,',').'}' : null;
    }

    /**
     * @param string $string
     * @return boolean
     */
    public static function isPGArray($string) {
        return preg_match('/^{[^}]+}$/im', $string);
    }

    /**
     * @param string[] $strings
     * @return string
     */
    public static function quoteStringsArray(array $strings)
    {
        $wkArr = $strings;
        foreach($wkArr as &$string)
            $string = '\''.str_replace('\'','\'\'',$string).'\'';
        return implode(',',$wkArr);
    }

} 