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

class TagProcessorTest extends TestCase
{
    /**
     * @covers Icoverlog\Processor\TagProcessor::__invoke
     */
    public function testProcessor()
    {
        $tags = [1, 2, 3];
        $processor = new TagProcessor($tags);
        $record = $processor($this->getRecord());

        $this->assertEquals($tags, $record->extra['tags']);
    }

    /**
     * @covers Icoverlog\Processor\TagProcessor::__invoke
     */
    public function testProcessorTagModification()
    {
        $tags = [1, 2, 3];
        $processor = new TagProcessor($tags);

        $record = $processor($this->getRecord());
        $this->assertEquals($tags, $record->extra['tags']);

        $processor->setTags(['a', 'b']);
        $record = $processor($this->getRecord());
        $this->assertEquals(['a', 'b'], $record->extra['tags']);

        $processor->addTags(['a', 'c', 'foo' => 'bar']);
        $record = $processor($this->getRecord());
        $this->assertEquals(['a', 'b', 'a', 'c', 'foo' => 'bar'], $record->extra['tags']);
    }
}
