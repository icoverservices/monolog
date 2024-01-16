<?php declare(strict_types=1);

/*
 * This file is part of the Icoverlog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icoverlog\Handler;

use Icoverlog\Level;
use Icoverlog\Test\TestCase;

class MailHandlerTest extends TestCase
{
    /**
     * @covers Icoverlog\Handler\MailHandler::handleBatch
     */
    public function testHandleBatch()
    {
        $formatter = $this->createMock('Icoverlog\\Formatter\\FormatterInterface');
        $formatter->expects($this->once())
            ->method('formatBatch'); // Each record is formatted

        $handler = $this->getMockForAbstractClass('Icoverlog\\Handler\\MailHandler', [], '', true, true, true, ['send', 'write']);
        $handler->expects($this->once())
            ->method('send');
        $handler->expects($this->never())
            ->method('write'); // write is for individual records

        $handler->setFormatter($formatter);

        $handler->handleBatch($this->getMultipleRecords());
    }

    /**
     * @covers Icoverlog\Handler\MailHandler::handleBatch
     */
    public function testHandleBatchNotSendsMailIfMessagesAreBelowLevel()
    {
        $records = [
            $this->getRecord(Level::Debug, 'debug message 1'),
            $this->getRecord(Level::Debug, 'debug message 2'),
            $this->getRecord(Level::Info, 'information'),
        ];

        $handler = $this->getMockForAbstractClass('Icoverlog\\Handler\\MailHandler');
        $handler->expects($this->never())
            ->method('send');
        $handler->setLevel(Level::Error);

        $handler->handleBatch($records);
    }

    /**
     * @covers Icoverlog\Handler\MailHandler::write
     */
    public function testHandle()
    {
        $handler = $this->getMockForAbstractClass('Icoverlog\\Handler\\MailHandler');
        $handler->setFormatter(new \Icoverlog\Formatter\LineFormatter);

        $record = $this->getRecord();
        $records = [$record];
        $records[0]['formatted'] = '['.$record->datetime.'] test.WARNING: test [] []'."\n";

        $handler->expects($this->once())
            ->method('send')
            ->with($records[0]['formatted'], $records);

        $handler->handle($record);
    }
}
