<?php declare(strict_types=1);

/*
 * This file is part of the Icoverlog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icoverlog\Formatter;

use Icoverlog\Level;
use Icoverlog\Test\TestCase;

class FlowdockFormatterTest extends TestCase
{
    /**
     * @covers Icoverlog\Formatter\FlowdockFormatter::format
     */
    public function testFormat()
    {
        $formatter = new FlowdockFormatter('test_source', 'source@test.com');
        $record = $this->getRecord();

        $expected = [
            'source' => 'test_source',
            'from_address' => 'source@test.com',
            'subject' => 'in test_source: WARNING - test',
            'content' => 'test',
            'tags' => ['#logs', '#warning', '#test'],
            'project' => 'test_source',
        ];
        $formatted = $formatter->format($record);

        $this->assertEquals($expected, $formatted);
    }

    /**
     * @ covers Icoverlog\Formatter\FlowdockFormatter::formatBatch
     */
    public function testFormatBatch()
    {
        $formatter = new FlowdockFormatter('test_source', 'source@test.com');
        $records = [
            $this->getRecord(Level::Warning),
            $this->getRecord(Level::Debug),
        ];
        $formatted = $formatter->formatBatch($records);

        $this->assertArrayHasKey('from_address', $formatted[0]);
        $this->assertArrayHasKey('from_address', $formatted[1]);
    }
}
