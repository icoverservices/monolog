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

use Exception;
use Icoverlog\LogRecord;

class ExceptionTestHandler extends TestHandler
{
    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        throw new Exception("ExceptionTestHandler::handle");
    }
}
