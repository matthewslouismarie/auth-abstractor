<?php

declare(strict_types=1);

namespace Tests\LM;

use InvalidArgumentException;
use LM\AuthAbstractor\Implementation\U2fRegistration;
use LM\AuthAbstractor\Model\IU2fRegistration;
use LM\Common\Enum\Scalar;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\IntegerObject;
use PHPUnit\Framework\TestCase;

class ArrayObjectTest extends TestCase
{
    public function testArrayObject()
    {
        $strings = [
            "string1",
            "string2",
            "string3",
        ];
        $arrayObject = new ArrayObject($strings, Scalar::_STR);
        $this->assertSame(3, $arrayObject->getSize());
    }

    public function testSupportForIntegers()
    {
        $ints = [
            5,
            2,
        ];
        $arrayObject = new ArrayObject($ints, Scalar::_INT);
        $this->assertSame(2, $arrayObject->getSize());
        $this->assertSame(5, $arrayObject->get(0, Scalar::_INT));
        $this->assertSame(2, $arrayObject->get(1, SCALAR::_INT));
        $arrayObject->get(1);
    }

    public function testSupportForKeys()
    {
        $arrayObject = new ArrayObject([
            'username' => 'jcdenton',
            'password' => 'bionicman',
        ], Scalar::_STR);
        $this->assertSame('jcdenton', $arrayObject->get('username', Scalar::_STR));
        $newArrayObject = $arrayObject->addWithKey('occupation', 'federal agent', Scalar::_STR);
        $this->assertSame('federal agent', $newArrayObject->get('occupation', Scalar::_STR));
    }

    public function testTypeCheckingGetCurrentItem()
    {
        $strings = [
            "Hello you",
        ];
        $arrayObject = new ArrayObject($strings, Scalar::_STR);
        $this->expectException(InvalidArgumentException::class);
        $arrayObject->getCurrentItem(Scalar::_INT);
    }

    public function testInvalidArrayObjects()
    {
        $strings = [
            "string1",
            "string2",
            5,
        ];
        $this->expectException(InvalidArgumentException::class);
        $arrayObject = new ArrayObject($strings, Scalar::_STR);
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
            $arrayObject->getCurrentItem(IntegerObject::class)->toInteger()
        )
        ;
        $this->assertNotSame(
            25,
            $arrayObject->getCurrentItem(IntegerObject::class)->toInteger()
        )
        ;
        $arrayObject->setToNextItem();
        $this->assertSame(
            25,
            $arrayObject->getCurrentItem(IntegerObject::class)->toInteger()
        )
        ;
        $unserialized = unserialize(serialize($arrayObject));

        $this->assertSame(
            25,
            $arrayObject->getCurrentItem(IntegerObject::class)->toInteger()
        )
        ;
    }

    public function testInterfaces()
    {
        $array = [
            new U2fRegistration('', 0, '', ''),
        ];
        $list = new ArrayObject($array, IU2fRegistration::class);
        $this->assertSame(
            1,
            $list->getSize()
        )
        ;
    }
}
