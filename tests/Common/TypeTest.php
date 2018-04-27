<?php

declare(strict_types=1);

namespace Tests\LM;

use InvalidArgumentException;
use LM\Common\Type\Type;
use PHPUnit\Framework\TestCase;
use LM\Common\Enum\Scalar;

class TypeTest extends TestCase
{
    public function testInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        $type = new Type('Invalid\Type');
    }

    public function testStringType()
    {
        $type = new Type(Scalar::_STR);
        $this->assertTrue($type->isStringType());
        $this->assertFalse($type->isArrayType());
        $this->assertFalse($type->isBoolType());
        $this->assertFalse($type->isClassOrInterfaceName());
        $this->assertFalse($type->isIntegerType());
        $type->check('just a string');
        $this->expectException(InvalidArgumentException::class);
        $type->check(3);
    }
}
