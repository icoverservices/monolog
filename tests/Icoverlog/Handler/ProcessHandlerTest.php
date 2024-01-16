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

use Icoverlog\Test\TestCase;
use Icoverlog\Level;

class ProcessHandlerTest extends TestCase
{
    /**
     * Dummy command to be used by tests that should not fail due to the command.
     *
     * @var string
     */
    const DUMMY_COMMAND = 'echo';

    /**
     * @covers Icoverlog\Handler\ProcessHandler::__construct
     * @covers Icoverlog\Handler\ProcessHandler::guardAgainstInvalidCommand
     * @covers Icoverlog\Handler\ProcessHandler::guardAgainstInvalidCwd
     * @covers Icoverlog\Handler\ProcessHandler::write
     * @covers Icoverlog\Handler\ProcessHandler::ensureProcessIsStarted
     * @covers Icoverlog\Handler\ProcessHandler::startProcess
     * @covers Icoverlog\Handler\ProcessHandler::handleStartupErrors
     */
    public function testWriteOpensProcessAndWritesToStdInOfProcess()
    {
        $fixtures = [
            'chuck norris',
            'foobar1337',
        ];

        $mockBuilder = $this->getMockBuilder('Icoverlog\Handler\ProcessHandler');
        $mockBuilder->onlyMethods(['writeProcessInput']);
        // using echo as command, as it is most probably available
        $mockBuilder->setConstructorArgs([self::DUMMY_COMMAND]);

        $handler = $mockBuilder->getMock();

        $matcher = $this->exactly(2);
        $handler->expects($matcher)
            ->method('writeProcessInput')
            ->willReturnCallback(function () use ($matcher, $fixtures) {
                match ($matcher->numberOfInvocations()) {
                    1 =>  $this->stringContains($fixtures[0]),
                    2 =>  $this->stringContains($fixtures[1]),
                };
            })
        ;

        /** @var ProcessHandler $handler */
        $handler->handle($this->getRecord(Level::Warning, $fixtures[0]));
        $handler->handle($this->getRecord(Level::Error, $fixtures[1]));
    }

    /**
     * Data provider for invalid commands.
     */
    public static function invalidCommandProvider(): array
    {
        return [
            [1337, 'TypeError'],
            ['', 'InvalidArgumentException'],
            [null, 'TypeError'],
            [fopen('php://input', 'r'), 'TypeError'],
        ];
    }

    /**
     * @dataProvider invalidCommandProvider
     * @param mixed $invalidCommand
     * @covers Icoverlog\Handler\ProcessHandler::guardAgainstInvalidCommand
     */
    public function testConstructWithInvalidCommandThrowsInvalidArgumentException($invalidCommand, $expectedExcep)
    {
        $this->expectException($expectedExcep);
        new ProcessHandler($invalidCommand, Level::Debug);
    }

    /**
     * Data provider for invalid CWDs.
     */
    public static function invalidCwdProvider(): array
    {
        return [
            [1337, 'TypeError'],
            ['', 'InvalidArgumentException'],
            [fopen('php://input', 'r'), 'TypeError'],
        ];
    }

    /**
     * @dataProvider invalidCwdProvider
     * @param mixed $invalidCwd
     * @covers Icoverlog\Handler\ProcessHandler::guardAgainstInvalidCwd
     */
    public function testConstructWithInvalidCwdThrowsInvalidArgumentException($invalidCwd, $expectedExcep)
    {
        $this->expectException($expectedExcep);
        new ProcessHandler(self::DUMMY_COMMAND, Level::Debug, true, $invalidCwd);
    }

    /**
     * @covers Icoverlog\Handler\ProcessHandler::__construct
     * @covers Icoverlog\Handler\ProcessHandler::guardAgainstInvalidCwd
     */
    public function testConstructWithValidCwdWorks()
    {
        $handler = new ProcessHandler(self::DUMMY_COMMAND, Level::Debug, true, sys_get_temp_dir());
        $this->assertInstanceOf(
            'Icoverlog\Handler\ProcessHandler',
            $handler,
            'Constructed handler is not a ProcessHandler.'
        );
    }

    /**
     * @covers Icoverlog\Handler\ProcessHandler::handleStartupErrors
     */
    public function testStartupWithFailingToSelectErrorStreamThrowsUnexpectedValueException()
    {
        $mockBuilder = $this->getMockBuilder('Icoverlog\Handler\ProcessHandler');
        $mockBuilder->onlyMethods(['selectErrorStream']);
        $mockBuilder->setConstructorArgs([self::DUMMY_COMMAND]);

        $handler = $mockBuilder->getMock();

        $handler->expects($this->once())
            ->method('selectErrorStream')
            ->will($this->returnValue(false));

        $this->expectException(\UnexpectedValueException::class);
        /** @var ProcessHandler $handler */
        $handler->handle($this->getRecord(Level::Warning, 'stream failing, whoops'));
    }

    /**
     * @covers Icoverlog\Handler\ProcessHandler::handleStartupErrors
     * @covers Icoverlog\Handler\ProcessHandler::selectErrorStream
     */
    public function testStartupWithErrorsThrowsUnexpectedValueException()
    {
        $handler = new ProcessHandler('>&2 echo "some fake error message"');

        $this->expectException(\UnexpectedValueException::class);

        $handler->handle($this->getRecord(Level::Warning, 'some warning in the house'));
    }

    /**
     * @covers Icoverlog\Handler\ProcessHandler::write
     */
    public function testWritingWithErrorsOnStdOutOfProcessThrowsInvalidArgumentException()
    {
        $mockBuilder = $this->getMockBuilder('Icoverlog\Handler\ProcessHandler');
        $mockBuilder->onlyMethods(['readProcessErrors']);
        // using echo as command, as it is most probably available
        $mockBuilder->setConstructorArgs([self::DUMMY_COMMAND]);

        $handler = $mockBuilder->getMock();

        $handler->expects($this->exactly(2))
            ->method('readProcessErrors')
            ->willReturnOnConsecutiveCalls('', $this->returnValue('some fake error message here'));

        $this->expectException(\UnexpectedValueException::class);
        /** @var ProcessHandler $handler */
        $handler->handle($this->getRecord(Level::Warning, 'some test stuff'));
    }

    /**
     * @covers Icoverlog\Handler\ProcessHandler::close
     */
    public function testCloseClosesProcess()
    {
        $class = new \ReflectionClass('Icoverlog\Handler\ProcessHandler');
        $property = $class->getProperty('process');
        $property->setAccessible(true);

        $handler = new ProcessHandler(self::DUMMY_COMMAND);
        $handler->handle($this->getRecord(Level::Warning, '21 is only the half truth'));

        $process = $property->getValue($handler);
        $this->assertTrue(is_resource($process), 'Process is not running although it should.');

        $handler->close();

        $process = $property->getValue($handler);
        $this->assertFalse(is_resource($process), 'Process is still running although it should not.');
    }
}
