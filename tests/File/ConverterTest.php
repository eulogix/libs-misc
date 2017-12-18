<?php
/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Lib\Misc\Tests\File;

use Eulogix\Lib\File\Converter\Html2PdfConverter;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testHtml2Pdf()
    {
        $c = new Html2PdfConverter();
        $c->checkEnvironment();

        $input = SimpleFileProxy::fromFileSystem(__DIR__.'/../res/html/simpledoc.html');
        $output = $c->convert($input, 'pdf');
        $this->assertTrue( $output->getSize() > 5000 );
    }
}