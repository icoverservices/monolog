<?php declare(strict_types=1);

/*
 * This file is part of the Icoverlog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icoverlog\Processor;

use Icoverlog\Test\TestCase;

class UidProcessorTest extends TestCase
{
    /**
     * @covers Icoverlog\Processor\UidProcessor::__invoke
     */
    public function testProcessor()
    {
        $processor = new UidProcessor();
        $record = $processor($this->getRecord());
        $this->assertArrayHasKey('uid', $record->extra);
    }

    public function testGetUid()
    {
        $processor = new UidProcessor(10);
        $this->assertEquals(10, strlen($processor->getUid()));
    }
}
