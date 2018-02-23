<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Crypto;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CryptoUtils
{
    const DEFAULT_PASSWORD = "25c6c7ff35b9979b151f2136cd13b0ff";

    /**
     * returns a decryptable encrypted string
     * @param string $string
     * @param string|null $password
     * @return string
     */
    public static function getEncryptedString($string, $password = self::DEFAULT_PASSWORD) {
        return '[#'.@openssl_encrypt($string, "AES-256-CBC", $password).'#]';
    }

    /**
     * decrypts a previously encrypted string
     * @param $code
     * @param string|null $password
     * @return bool|string
     */
    public static function decodeEncryptedString($code, $password = self::DEFAULT_PASSWORD) {
        if(!preg_match('%^\[#([a-z0-9+ /=]+)#]$%im', $code, $m))
            return false;
        return openssl_decrypt($m[1], "AES-256-CBC", $password);
    }

    /**
     * @param string $text
     * @return array
     */
    public static function decodeAllEncryptedStringsFromText($text) {
        $ret = [];
        if(!preg_match_all('%\[#[a-z0-9+ /=]+#]%im', $text, $m, PREG_PATTERN_ORDER))
            return $ret;

        foreach($m[0] as $key => $code)
            $ret[ $code ] = self::decodeEncryptedString( $code );

        return($ret);
    }

    /**
     * @param string $text
     * @return string
     */
    public static function removeAllEncryptedStringsFromText($text) {
        return preg_replace('%\[#[a-z0-9+ /=]+#]%im', '', $text);
    }
}
