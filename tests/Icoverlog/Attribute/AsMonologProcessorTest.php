<?php declare(strict_types=1);

/*
 * This file is part of the Icoverlog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icoverlog\Attribute;

use PHPUnit\Framework\TestCase;

/**
 * @requires PHP 8.0
 */
final class AsIcoverlogProcessorTest extends TestCase
{
    public function test(): void
    {
        $asIcoverlogProcessor = new AsIcoverlogProcessor('channel', 'handler', 'method', -10);
        $this->assertSame('channel', $asIcoverlogProcessor->channel);
        $this->assertSame('handler', $asIcoverlogProcessor->handler);
        $this->assertSame('method', $asIcoverlogProcessor->method);
        $this->assertSame(-10, $asIcoverlogProcessor->priority);

        $asIcoverlogProcessor = new AsIcoverlogProcessor(null, null, null, null);
        $this->assertNull($asIcoverlogProcessor->channel);
        $this->assertNull($asIcoverlogProcessor->handler);
        $this->assertNull($asIcoverlogProcessor->method);
        $this->assertNull($asIcoverlogProcessor->priority);
    }
}
