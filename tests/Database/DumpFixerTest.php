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

use Eulogix\Lib\Database\Postgres\DumpFixer;
use Eulogix\Lib\Progress\Event\ProgressEvent;
use Eulogix\Lib\Progress\ProgressTracker;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ProgressTest extends \PHPUnit_Framework_TestCase
{

    public function testDumpFixer()
    {
        $fixerOld = new DumpFixer(__DIR__.'/../res/sql/dump_963.sql');
        $fixerNew = new DumpFixer(__DIR__.'/../res/sql/dump_968.sql');

        $this->assertTrue( $fixerNew->mustBeFixed() );
        $this->assertTrue( !$fixerOld->mustBeFixed() );

        file_put_contents(__DIR__.'/../res/sql/dump_modded.sql', $fixerNew->getFixedDump());

        $fixerModded = new DumpFixer($fixerNew->getFixedDump());
        $this->assertTrue( !$fixerModded->mustBeFixed() );
    }

}