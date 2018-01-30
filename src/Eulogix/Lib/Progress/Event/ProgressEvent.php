<?php
/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Progress\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ProgressEvent extends Event
{
    /**
     * @var int
     */
    protected $progressPercentage;

    /**
     * @var string
     */
    protected $context;

    /**
     * ProgressEvent constructor.
     * @param float $progressPercentage
     * @param string $context
     */
    public function __construct($progressPercentage, $context = null)
    {
        $this->progressPercentage = $progressPercentage;
        $this->context = $context;
    }

    /**
     * @return int
     */
    public function getProgressPercentage()
    {
        return $this->progressPercentage;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }
}