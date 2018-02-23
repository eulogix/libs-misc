<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Email;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class EmailUtils {

    /**
     * @param $text
     * @return array
     */
    public static function extractAllValidEmailAddressesFrom($text) {
        $emailRegex = '/([a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)/i';
        return preg_match_all($emailRegex, $text, $m) ? $m[1] : [];
    }
}