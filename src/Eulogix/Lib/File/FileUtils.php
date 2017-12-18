<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\File;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileUtils
{
    /**
     * @param string $extension
     * @param string $folder
     * @return string
     */
    public static function getTempFileName($folder=null, $extension=null) {
        $t = tempnam($folder ?? sys_get_temp_dir(), '');
        @unlink($t);
        return $t.($extension ? ".$extension" : '');
    }

    /**
     * @param string $folder
     * @return string
     */
    public static function getTempFolder($folder = null)
    {
        $tf = self::getTempFileName($folder);
        mkdir($tf);
        return $tf;
    }
}