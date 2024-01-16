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

/**
 * @covers Icoverlog\Handler\NoopHandler::handle
 */
class NoopHandlerTest extends TestCase
{
    /**
     * @dataProvider logLevelsProvider
     */
    public function testIsHandling(Level $level)
    {
        $handler = new NoopHandler();
        $this->assertTrue($handler->isHandling($this->getRecord($level)));
    }

    /**
     * @dataProvider logLevelsProvider
     */
    public function testHandle(Level $level)
    {
        $handler = new NoopHandler();
        $this->assertFalse($handler->handle($this->getRecord($level)));
    }

    public static function logLevelsProvider()
    {
        return array_map(
            fn ($level) => [$level],
            Level::cases()
        );
    }
}
