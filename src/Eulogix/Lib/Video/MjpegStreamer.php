<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Video;

/**
 * Streams a mjpeg file to a web browser
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 *
 */

class MjpegStreamer {

    /**
     * @param string $file
     * @param int $frameRate
     * @param string $boundaryName
     */
    public static function streamFile($file, $frameRate, $boundaryName='ffserver') {
        self::sendHeaders($boundaryName);

        $frameDelay = ceil(1000000/$frameRate);

        $fp = fopen($file, 'r');
        $buffer = '';
        if ($fp) {
            while (!feof($fp)) {
                $buffer .= fgets($fp, 4096);

                //1 is to offset it from the file start
                $nextBoundaryPos = strpos($buffer, '--'.$boundaryName, 1);
                if($nextBoundaryPos !== false) {
                    $imgChunk = substr($buffer, 0, $nextBoundaryPos);
                    echo $imgChunk;
                    usleep($frameDelay);
                    $buffer = substr($buffer, $nextBoundaryPos);
                }

            }
            echo $buffer;
            fclose($fp);
        }
    }

    /**
     * @param string $boundaryName
     */
    private static function sendHeaders($boundaryName) {
        header("Cache-Control: no-cache");
        header("Cache-Control: private");
        header("Pragma: no-cache");
        header("Content-type: multipart/x-mixed-replace; boundary=$boundaryName");
    }

} 