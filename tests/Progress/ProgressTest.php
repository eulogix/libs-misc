<?php
/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Lib\Misc\Tests\Progress;

use Eulogix\Lib\Progress\Event\ProgressEvent;
use Eulogix\Lib\Progress\ProgressTracker;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ProgressTest extends \PHPUnit_Framework_TestCase
{

    public function testProgress()
    {
        $p = new ProgressTracker(new EventDispatcher());

        $p->getDispatcher()->addListener( ProgressTracker::EVENT_PROGRESS,
            function(ProgressEvent $e) {
                echo floor($e->getProgressPercentage())."\n";
            }
        );

        $c1 = new branchClass($p);
        $p->openSub(70);
        $c1->nestF();
        $p->closeSub();

        $p->openSub(30);
        $c2 = new branchClass($p);
        $c2->nestF();
        $p->closeSub();

    }

}

class branchClass {
    /**
     * @var ProgressTracker
     */
    private $t;

    function __construct(ProgressTracker $t) {
        $this->t = $t;
    }

    public function nestF() {
        for($i = 1; $i<=4; $i++) {
            //echo '  F1  '.$i."\n";
            $this->t->openSub($weight = 25);
            $this->nestF2();
            $this->t->closeSub();
        }
    }

    public function nestF2() {
        for($i = 1; $i<=5; $i++) {
            //echo '  F2  '.$i."\n";
            $this->t->logProgress($i * 20);
        }
    }
}
