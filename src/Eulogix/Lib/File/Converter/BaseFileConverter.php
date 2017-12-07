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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class BaseFileConverter implements FileConverterInterface {

    protected $supportedConversions = [];

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