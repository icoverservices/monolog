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

class AbstractHandlerTest extends TestCase
{
    /**
     * @covers Icoverlog\Handler\AbstractHandler::__construct
     * @covers Icoverlog\Handler\AbstractHandler::getLevel
     * @covers Icoverlog\Handler\AbstractHandler::setLevel
     * @covers Icoverlog\Handler\AbstractHandler::getBubble
     * @covers Icoverlog\Handler\AbstractHandler::setBubble
     */
    public function testConstructAndGetSet()
    {
        $handler = $this->getMockForAbstractClass('Icoverlog\Handler\AbstractHandler', [Level::Warning, false]);
        $this->assertEquals(Level::Warning, $handler->getLevel());
        $this->assertEquals(false, $handler->getBubble());

        $handler->setLevel(Level::Error);
        $handler->setBubble(true);
        $this->assertEquals(Level::Error, $handler->getLevel());
        $this->assertEquals(true, $handler->getBubble());
    }

    /**
     * @covers Icoverlog\Handler\AbstractHandler::handleBatch
     */
    public function testHandleBatch()
    {
        $handler = $this->getMockForAbstractClass('Icoverlog\Handler\AbstractHandler');
        $handler->expects($this->exactly(2))
            ->method('handle');
        $handler->handleBatch([$this->getRecord(), $this->getRecord()]);
    }

    /**
     * @covers Icoverlog\Handler\AbstractHandler::isHandling
     */
    public function testIsHandling()
    {
        $handler = $this->getMockForAbstractClass('Icoverlog\Handler\AbstractHandler', [Level::Warning, false]);
        $this->assertTrue($handler->isHandling($this->getRecord()));
        $this->assertFalse($handler->isHandling($this->getRecord(Level::Debug)));
    }

    /**
     * @covers Icoverlog\Handler\AbstractHandler::__construct
     */
    public function testHandlesPsrStyleLevels()
    {
        $handler = $this->getMockForAbstractClass('Icoverlog\Handler\AbstractHandler', ['warning', false]);
        $this->assertFalse($handler->isHandling($this->getRecord(Level::Debug)));
        $handler->setLevel('debug');
        $this->assertTrue($handler->isHandling($this->getRecord(Level::Debug)));
    }
}
