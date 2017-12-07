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
     * @param FileProxyInterface $input
     * @param array $options
     * @return FileProxyInterface
     */
    public function convert(FileProxyInterface $input, array $options);

}