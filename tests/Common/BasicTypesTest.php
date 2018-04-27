<?php

declare(strict_types=1);

namespace Tests\Common;

use PHPUnit\Framework\TestCase;
use LM\Common\Model\BooleanObject;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\StringObject;
use TypeError;

class BasicTypesTest extends TestCase
{
    public function testInvalidBooleanObject()
    {
        $this->expectException(TypeError::class);
        new BooleanObject([]);
    }

    public function testInvalidIntegerObject()
    {
        $this->expectException(TypeError::class);
        new IntegerObject([]);
    }

    public function testInvalidStringObject()
    {
        $this->expectException(TypeError::class);
        new StringObject([]);
    }

    public function testValidBooleanObject()
    {
        $bool = new BooleanObject(true);
        $this->assertSame(
            $bool->toBoolean(),
            unserialize(serialize($bool))->toBoolean()
        );
    }

    public function testValidIntegerObject()
    {
        $int = new IntegerObject(5);
        $this->assertSame(
            $int->toInteger(),
            unserialize(serialize($int))->toInteger()
        );
    }

    public function testValidStringObject()
    {
        $str = new StringObject('a string');
        $this->assertSame(
            'a string',
            unserialize(serialize($str))->toString()
        );
    }
}
