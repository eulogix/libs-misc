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
        $output = shell_exec('wkhtmltopdf -v');
        if(!preg_match("/wkhtmltopdf 0\\.12\\.[0-9]+/sim", $output))
            throw new \Exception("wkhtmltopdf 0.12.x not found in path");
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

        $cmd = "wkhtmltopdf";
        foreach($options as $option => $optionValue) {
            if($this->isOptionValid($option))
                $cmd.=" --$option \"$optionValue\"";
        }

        $cmd.=" \"{$sourceTemplate}\" \"{$tempTarget}\"";

        shell_exec($cmd);
        $ret = SimpleFileProxy::fromFileSystem($tempTarget, true);

        if($clearInput)
            @unlink($sourceTemplate);

        @unlink($tempTarget);

        return $ret;
    }

    protected function isOptionValid($option) {
        return in_array($option, [
            'collate',
            'no-collate',
            'cookie-jar',
            'copies',
            'dpi',
            'extended-help',
            'grayscale',
            'help',
            'htmldoc',
            'image-dpi',
            'image-quality',
            'license',
            'lowquality',
            'manpage',
            'margin-bottom',
            'margin-left',
            'margin-right',
            'margin-top',
            'orientation',
            'page-height',
            'page-size',
            'page-width',
            'no-pdf-compression',
            'quiet',
            'read-args-from-stdin',
            'readme',
            'title',
            'use-xserver',
            'version',
            'dump-default-toc-xsl',
            'dump-outline',
            'outline',
            'no-outline',
            'outline-depth',
            'allow',
            'background',
            'no-background',
            'bypass-proxy-for',
            'cache-dir',
            'checkbox-checked-svg',
            'checkbox-svg',
            'cookie',
            'custom-header',
            'custom-header-propagation',
            'custom-header',
            'no-custom-header-propagation',
            'custom-header',
            'debug-javascript',
            'no-debug-javascript',
            'default-header',
            'top',
            'header-line',
            'encoding',
            'disable-external-links',
            'enable-external-links',
            'disable-forms',
            'enable-forms',
            'images',
            'no-images',
            'disable-internal-links',
            'enable-internal-links',
            'disable-javascript',
            'enable-javascript',
            'javascript-delay',
            'keep-relative-links',
            'load-error-handling',
            'load-media-error-handling',
            'disable-local-file-access',
            'enable-local-file-access',
            'minimum-font-size',
            'exclude-from-outline',
            'include-in-outline',
            'page-offset',
            'password',
            'disable-plugins',
            'enable-plugins',
            'post',
            'post-file',
            'print-media-type',
            'no-print-media-type',
            'proxy',
            'radiobutton-checked-svg',
            'radiobutton-svg',
            'resolve-relative-links',
            'run-script',
            'disable-smart-shrinking',
            'enable-smart-shrinking',
            'stop-slow-scripts',
            'no-stop-slow-scripts',
            'disable-toc-back-links',
            'enable-toc-back-links',
            'user-style-sheet',
            'username',
            'viewport-size',
            'window-status',
            'zoom',
            'footer-center',
            'footer-font-name',
            'footer-font-size',
            'footer-html',
            'footer-left',
            'footer-line',
            'no-footer-line',
            'footer-right',
            'footer-spacing',
            'header-center',
            'header-font-name',
            'header-font-size',
            'header-html',
            'header-left',
            'header-line',
            'no-header-line',
            'header-right',
            'header-spacing',
            'replace',
            'disable-dotted-lines',
            'toc-header-text',
            'toc-level-indentation',
            'disable-toc-links',
            'toc-text-size-shrink',
            'xsl-style-sheet'
        ]);
    }
}