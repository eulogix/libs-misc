<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Cache;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

trait Cached {

    /**
     * @var CacherInterface
     */
    private $cacher;

    /**
     * @return CacherInterface
     */
    public function getCacher() {
        if(!$this->cacher)
            $this->cacher = new DummyCacher();
        return $this->cacher;
    }

    /**
     * @param CacherInterface $cacher
     */
    public function setCacher( CacherInterface $cacher ) {
        $this->cacher = $cacher;
    }

}