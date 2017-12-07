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

class Html2PdfConverter extends BaseFileConverter
{

    public function __construct() {
        $this->addSupportedConversion('html','pdf');
        $this->addSupportedConversion('htm','pdf');
    }

    /**
     * @param FileProxyInterface $input
     * @param array $options
     * @return FileProxyInterface
     */
    public function convert(FileProxyInterface $input, array $options)
    {
        // TODO: Implement convert() method.
    }
}