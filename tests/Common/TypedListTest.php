<?php

declare(strict_types=1);

namespace Tests\LM;

use InvalidArgumentException;
use LM\Common\DataStructure\TypedList;
use PHPUnit\Framework\TestCase;
use LM\Common\Enum\Scalar;

class TypedListTest extends TestCase
{
    public function testTypedList()
    {
        $list = new TypedList(
            [
                'value1',
                'value2',
                'value3',
            ],
            Scalar::_STR
        );
        $this->assertSame(3, count($list));
    }

    public function testNoItems()
    {
        $list = new TypedList(
            [],
            Scalar::_STR
        );
        $this->assertSame(0, count($list));
    }

    public function testValidAppend()
    {
        $list = new TypedList(
            [
                'value1',
            ],
            Scalar::_STR
        );
        $this->assertSame(2, count($list->append('value4')));
        $this->assertSame(1, count($list));
    }

    public function testInvalidAppend()
    {
        $list = new TypedList(
            [
                'value1',
            ],
            Scalar::_STR
        );
        $this->expectException(InvalidArgumentException::class);
        $list->append(3);
    }

    public function testInvalidConstruction()
    {
        $this->expectException(InvalidArgumentException::class);
        $list = new TypedList(
            [
                'value1',
                'value2',
                5,
            ],
            Scalar::_STR
        );
    }
}
