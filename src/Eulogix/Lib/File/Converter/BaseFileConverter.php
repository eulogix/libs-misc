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

use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class BaseFileConverter implements FileConverterInterface {

    protected $supportedConversions = [];

    /**
     * @inheritdoc
     */
    public function convert($input, $formatTo, array $options = [])
    {
        $wkInput = $input instanceof FileProxyInterface ? $input : SimpleFileProxy::fromFileSystem($input);
        if(!$this->supportsConversion($wkInput->getExtension(), $formatTo))
            throw new \Exception("Conversion not supported from ".$wkInput->getExtension()." to {$formatTo}");
        return $this->doConvert($input, $formatTo, $options);
    }

    /**
     * @param FileProxyInterface|string $input
     * @param string $formatTo
     * @param array $options
     * @return FileProxyInterface
     * @throws \Exception
     */
    protected abstract function doConvert($input, $formatTo, array $options = []);

    /**
     * @inheritdoc
     */
    public function supportsConversion($formatFrom, $formatTo)
    {
        return @$this->supportedConversions[strtolower($formatFrom)][strtolower($formatTo)] == 1;
    }

    /**
     * @param string $formatFrom
     * @param string $formatTo
     */
    protected function addSupportedConversion($formatFrom, $formatTo) {
        $this->supportedConversions[strtolower($formatFrom)][strtolower($formatTo)] = 1;
    }

}