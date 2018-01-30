<?php
/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Progress;

use Eulogix\Lib\Progress\Event\ProgressEvent;
use Eulogix\Lib\Traits\DispatcherHolder;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ProgressTracker
{
    use DispatcherHolder;

    const EVENT_PROGRESS = "PROGRESS";

    /**
     * @var float
     */
    private $currentProgress = 0;

    /**
     * @var ProgressTracker
     */
    private $parent, $tip;

    /**
     * @return ProgressTracker
     */
    public function getTip()
    {
        return $this->tip ?? $this;
    }

    /**
     * @param ProgressTracker $tip
     * @return $this
     */
    public function setTip($tip)
    {
        $this->tip = $tip;
        return $this;
    }

    /**
     * @return ProgressTracker
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param ProgressTracker $parent
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    public function reset() {
        $this->setCurrentProgress(0);
        unset($this->tip);
        unset($this->parent);
    }

    public function getCurrentProgress()
    {
        return $this->currentProgress;
    }

    /**
     * @param int $currentProgress
     * @return $this
     */
    public function setCurrentProgress($currentProgress)
    {
        $this->currentProgress = $currentProgress;
        return $this;
    }

    /**
     * @param float $branchWeight
     * @return ProgressTracker
     */
    public function openSub($branchWeight) {
        $subPT = new ProgressTracker();
        $subPT->setParent($this->getTip());
        $startProgress = $this->getTip()->getCurrentProgress();

        $subPT->getDispatcher()->addListener( self::EVENT_PROGRESS,
            function(ProgressEvent $e) use ($subPT, $branchWeight, $startProgress) {
                $subPT->getParent()->logProgress($startProgress + ($branchWeight/100)*$e->getProgressPercentage(), true);
            }
        );

        $this->setTip($subPT);
    }

    public function closeSub() {
        if($tip = $this->tip) {
            $this->setTip($tip->getParent());
            unset($tip);
        }
    }

    /**
     * @param $progressPercentage
     * @param bool $self
     */
    public function logProgress($progressPercentage, $self = false) {
        $tracker = $self ? $this : $this->getTip();
        $tracker->setCurrentProgress($progressPercentage);
        $tracker->getDispatcher()->dispatch(self::EVENT_PROGRESS, new ProgressEvent($progressPercentage));
    }

}