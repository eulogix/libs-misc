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

use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ZipUtils
{
    /**
     * @throws \Exception
     */
    public static function checkEnvironment()
    {
        $output = shell_exec('zip -v');
        if(!preg_match("/Info-ZIP/sim", $output))
            throw new \Exception("zip command not found in PATH");
        $output = shell_exec('unzip -v');
        if(!preg_match("/Info-ZIP/sim", $output))
            throw new \Exception("unzip command not found in PATH");
    }

    /**
     * @param string $folder
     * @return SimpleFileProxy
     * @throws \Exception
     */
    public static function zipFolder($folder) {
        self::checkEnvironment();

        if(!file_exists($folder) || !is_dir($folder))
            throw new \Exception("$folder does not exist or is not a directory");

        $tempArchive = FileUtils::getTempFileName(null, 'zip');
        $cmd = "cd \"$folder\" && zip -r \"$tempArchive\" *";
        shell_exec($cmd);
        return SimpleFileProxy::fromFileSystem($tempArchive);
    }

    /**
     * @param FileProxyInterface $archive
     * @param string $targetFolder
     * @throws \Exception
     */
    public static function unpack(FileProxyInterface $archive, $targetFolder) {
        self::checkEnvironment();

        if(!file_exists($targetFolder) || !is_dir($targetFolder))
            throw new \Exception("$targetFolder does not exist or is not a directory");

        $tempArchive = FileUtils::getTempFileName(null, 'zip');
        $archive->toFile($tempArchive);

        $cmd = "unzip \"$tempArchive\" -d \"$targetFolder\"";
        shell_exec($cmd);
        @unlink($tempArchive);
    }

    /**
     * @param FileProxyInterface $archive
     * @throws \Exception
     * @return array
     */
    public static function getContentList(FileProxyInterface $archive) {
        $tempArchive = FileUtils::getTempFileName(null, 'zip');
        $archive->toFile($tempArchive);

        $content = [];
        if ($zip = zip_open($tempArchive)) {
            while ($zip_entry = zip_read($zip))
                $content[] = zip_entry_name($zip_entry);
            zip_close($zip);
        }

        @unlink($tempArchive);

        return $content;
    }
}