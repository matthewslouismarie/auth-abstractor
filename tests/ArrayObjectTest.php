<?php

namespace LM\Authentifier\Tests;

use PHPUnit\Framework\TestCase;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\IntegerObject;

use InvalidArgumentException;

class ArrayObjectTest extends TestCase
{
    public function testArrayObject()
    {
        $strings = [
            "string1",
            "string2",
            "string3",
        ];
        $arrayObject = new ArrayObject($strings, 'string');
        $this->assertSame(3, $arrayObject->getSize());
    }

    public function testSupportForIntegers()
    {
        $ints = [
            5,
            2,
        ];
        $arrayObject = new ArrayObject($ints, 'int');
        $this->assertSame(2, $arrayObject->getSize());
    }

    public function testTypeCheckingGetCurrentItem()
    {
        $strings = [
            "Hello you",
        ];
        $arrayObject = new ArrayObject($strings, 'string');
        $this->expectException(InvalidArgumentException::class);
        $arrayObject->getCurrentItem('int');
    }

    public function testInvalidArrayObjects()
    {
        $strings = [
            "string1",
            "string2",
            5,
        ];
        $this->expectException(InvalidArgumentException::class);
        $arrayObject = new ArrayObject($strings, 'string');
    }

    public function testInvalidType()
    {
        $strings = [
            "string1",
            "string2",
            "string3",
        ];
        $this->expectException(InvalidArgumentException::class);
        $arrayObject = new ArrayObject($strings, 'RandomClassName');
    }

    public function testGetCurrentItem()
    {
        $arrayObject = new ArrayObject([
            new IntegerObject(5),
            new IntegerObject(25),
            new IntegerObject(45),
        ], IntegerObject::class);
        $this->assertSame(
            5,
            $arrayObject->getCurrentItem(IntegerObject::class)->toInteger())
        ;
        $this->assertNotSame(
            25,
            $arrayObject->getCurrentItem(IntegerObject::class)->toInteger())
        ;
        $arrayObject->setToNextItem();
        $this->assertSame(
            25,
            $arrayObject->getCurrentItem(IntegerObject::class)->toInteger())
        ;
        $unserialized = unserialize(serialize($arrayObject));

        $this->assertSame(
            25,
            $arrayObject->getCurrentItem(IntegerObject::class)->toInteger())
        ;
    }
}
