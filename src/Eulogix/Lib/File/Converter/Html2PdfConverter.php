<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\File\Converter;

use Eulogix\Lib\File\FileUtils;
use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;

class Html2PdfConverter extends BaseFileConverter
{

    const OPTION_ZOOM = 'zoom';
    const OPTION_PAGE_FORMAT = 'page_format';

    public function __construct() {
        $this->addSupportedConversion('html','pdf');
        $this->addSupportedConversion('htm','pdf');
    }

    /**
     * if the converter needs binaries, extensions or other things,
     * check it here and throw an exception if requirements are not met
     * @throws \Exception
     */
    public function checkEnvironment()
    {
        $output = shell_exec('phantomjs -v');
        if(!preg_match("/^2\\.[0-9]+\\.[0-9]+/sim", $output))
            throw new \Exception("PhantomJS not found, or incorrect version. 2.x required, see: http://phantomjs.org/download.html");
    }

    /**
     * @inheritdoc
     */
    protected function doConvert($input, $formatTo, array $options = [])
    {
        $clearInput = false;

        if($input instanceof FileProxyInterface) {
            $sourceTemplate = FileUtils::getTempFileName(null, 'htm');
            $input->toFile($sourceTemplate);
            $clearInput = true;
        } else $sourceTemplate = $input;

        $tempTarget = FileUtils::getTempFileName(null, 'pdf');

        $rasterizeJs = __DIR__.'/res/phantomjs/rasterize.js';
        $cmd = "phantomjs \"{$rasterizeJs}\" \"file:///{$sourceTemplate}\" \"{$tempTarget}\"";
        if($pageFormat = @$options[self::OPTION_PAGE_FORMAT])
            $cmd.= " $pageFormat";
        if($zoom = @$options[self::OPTION_ZOOM])
            $cmd.= " $zoom";
        shell_exec($cmd);
        $ret = SimpleFileProxy::fromFileSystem($tempTarget, true);

        if($clearInput)
            @unlink($sourceTemplate);

        @unlink($tempTarget);

        return $ret;
    }
}