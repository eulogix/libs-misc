<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Lib\Traits;

use Eulogix\Lib\Progress\ProgressTracker;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

trait ProgressTrackerHolder {

    /**
     * @var ProgressTracker
     */
    private $progressTracker;


    /**
     * @return ProgressTracker
     */
    public function getProgressTracker() {
        return $this->progressTracker ?? ($this->progressTracker = new ProgressTracker());
    }

    /**
     * @param ProgressTracker $progressTracker
     * @return $this
     */
    public function setProgressTracker($progressTracker)
    {
        $this->progressTracker = $progressTracker;
        return $this;
    }

}