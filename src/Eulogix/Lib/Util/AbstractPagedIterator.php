<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Util;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use Iterator;
use LogicException;
use OutOfBoundsException;

/**
 * Paged iterator, useful to provide an iterator interface to webservices, database queries...
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 *
 */

abstract class AbstractPagedIterator implements Countable, ArrayAccess, Iterator
{
    /**
     * @var array
     */
    protected $cachedPages = [];

    /**
     * @var integer
     */
    protected $index = 0;

    /**
     * @return integer
     */
    abstract public function getPageSize();

    /**
     * @return integer
     */
    abstract public function getTotalSize();

    /**
     * @param integer $pageNumber
     * @return array
     */
    abstract public function doGetPage($pageNumber);

    /**
     * @param integer $pageNumber
     * @return array
     */
    public function getPage($pageNumber) {
        if( isset($this->cachedPages[$pageNumber]) )
            return $this->cachedPages[$pageNumber];
        return $this->cachePage($pageNumber);
    }

    /**
     * @return integer
     */
    public function count()
    {
        return $this->getTotalSize();
    }

    /**
     * @return integer
     */
    public function countPages()
    {
        return ceil( $this->getTotalSize() / $this->getPageSize() );
    }

    /**
     * @param integer $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $offset >= 0 && $offset < $this->getTotalSize();
    }

    /**
     * @param integer $offset
     * @return mixed
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        if (!is_int($offset)) {
            throw new InvalidArgumentException("Index must be a positive integer: $offset");
        }
        if (!$this->offsetExists($offset)) {
            throw new OutOfBoundsException("Index out of bounds: $offset");
        }

        $page = (int) ($offset / $this->getPageSize());
        if (!array_key_exists($page, $this->cachedPages)) {
            $this->cachePage($page);
        }
        return $this->cachedPages[$page][$offset % $this->getPageSize()];
    }

    /**
     * call this in the constructor to get the total count and cache the first page
     * @param int $page
     */
    public function cachePage($page) {
        $this->cachedPages[$page] = $this->doGetPage($page);
        return $this->cachedPages[$page];
    }

    /**
     * @param integer $offset
     * @param mixed $value
     * @throws LogicException
     */
    public function offsetSet($offset, $value)
    {
        throw new LogicException("Setting values is not allowed.");
    }

    /**
     * @param integer $offset
     */
    public function offsetUnset($offset)
    {
        throw new LogicException("Unsetting values is not allowed.");
    }

    public function current()
    {
        return $this->offsetGet($this->index);
    }

    public function key()
    {
        return $this->index;
    }

    public function next()
    {
        ++$this->index;
    }

    public function rewind()
    {
        $this->index = 0;
    }

    public function valid()
    {
        return $this->offsetExists($this->index);
    }
}