<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\File\Proxy;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface FileProxyInterface
{
    /**
     * gets the whole content as a string
     * @return mixed
     */
    public function getContent();

    /**
     * @return int
     */
    public function getSize();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getBaseName();

    /**
     * for files like file.htm.tmp, returns 'tmp'
     * @return string
     */
    public function getExtension();

    /**
     * for files like file.htm.tmp, returns 'htm.tmp'
     * @return string
     */
    public function getCompleteExtension();

    /**
     * @return array
     */
    public function getProperties();

    /**
     * @param array $properties
     * @return $this
     */
    public function setProperties($properties);

    /**
     * @param $propertyName
     * @return mixed
     */
    public function getProperty($propertyName);

    /**
     * @param $propertyName
     * @param $propertyValue
     * @return $this
     */
    public function setProperty($propertyName, $propertyValue);

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     * @return $this
     */
    public function setParentId($id);

    /**
     * @return mixed
     */
    public function getParentId();

    /**
     * @return mixed
     */
    public function getHash();

    /**
     * @return bool
     */
    public function isDirectory();

    /**
     * @return \DateTime
     */
    public function getCreationDate();

    /**
     * @return \DateTime
     */
    public function getLastModificationDate();

    /**
     * returns an array representation of the proxy, without the content
     * @return array
     */
    public function getArray();

    /**
     * @return boolean
     */
    public function isEmpty();

    /**
     * saves the file in the filesystem
     * @param string $fileName
     * @return $this
     */
    public function toFile($fileName);
}