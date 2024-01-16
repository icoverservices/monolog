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

class FluentdFormatterTest extends TestCase
{
    /**
     * @covers Icoverlog\Formatter\FluentdFormatter::__construct
     * @covers Icoverlog\Formatter\FluentdFormatter::isUsingLevelsInTag
     */
    public function testConstruct()
    {
        $formatter = new FluentdFormatter();
        $this->assertEquals(false, $formatter->isUsingLevelsInTag());
        $formatter = new FluentdFormatter(false);
        $this->assertEquals(false, $formatter->isUsingLevelsInTag());
        $formatter = new FluentdFormatter(true);
        $this->assertEquals(true, $formatter->isUsingLevelsInTag());
    }

    /**
     * @covers Icoverlog\Formatter\FluentdFormatter::format
     */
    public function testFormat()
    {
        $record = $this->getRecord(Level::Warning, datetime: new \DateTimeImmutable("@0"));

        $formatter = new FluentdFormatter();
        $this->assertEquals(
            '["test",0,{"message":"test","context":[],"extra":[],"level":300,"level_name":"WARNING"}]',
            $formatter->format($record)
        );
    }

    /**
     * @covers Icoverlog\Formatter\FluentdFormatter::format
     */
    public function testFormatWithTag()
    {
        $record = $this->getRecord(Level::Error, datetime: new \DateTimeImmutable("@0"));

        $formatter = new FluentdFormatter(true);
        $this->assertEquals(
            '["test.error",0,{"message":"test","context":[],"extra":[]}]',
            $formatter->format($record)
        );
    }
}
