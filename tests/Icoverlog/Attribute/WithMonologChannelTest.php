<?php

namespace Icoverlog\Attribute;

use PHPUnit\Framework\TestCase;

class WithIcoverlogChannelTest extends TestCase
{
    public function test(): void
    {
        $attribute = new WithIcoverlogChannel('fixture');
        $this->assertSame('fixture', $attribute->channel);
    }

}
