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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface FileConverterInterface {

    /**
     * @param string $formatFrom
     * @param string $formatTo
     * @return bool
     */
    public function supportsConversion($formatFrom, $formatTo);

    /**
     * @param FileProxyInterface|string $input
     * @param string $formatTo
     * @param array $options
     * @return FileProxyInterface
     * @throws \Exception
     */
    public function convert($input, $formatTo, array $options = []);

    /**
     * if the converter needs binaries, extensions or other things,
     * check it here and throw an exception if requirements are not met
     * @throws \Exception
     */
    public function checkEnvironment();

}