<?php

namespace LM\Authentifier\Tests;

use PHPUnit\Framework\TestCase;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\StringObject;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\RequestDatum;

class DataManagerTest extends TestCase
{
    public function testDataManager()
    {
        $array = [
            new RequestDatum("item0", new StringObject("Hi")),
            new RequestDatum("item1", new StringObject("Yo")),
            new RequestDatum("item2", new StringObject("Hey")),
        ];
        $dm = new DataManager($array);
        $this->assertSame(3, $dm->getSize());
        $item = new RequestDatum("item3", new IntegerObject(5));
        $retrievedItem = $dm
            ->add($item)
            ->get(RequestDatum::KEY_PROPERTY, "item3")
            ->getOnlyValue()
        ;
        $this->assertEquals($item, $retrievedItem);
        $this->assertSame(3, $dm
            ->get(RequestDatum::CLASS_PROPERTY, StringObject::class)
            ->getSize())
        ;
        $this->assertSame(0, $dm
            ->get(RequestDatum::CLASS_PROPERTY, IntegerObject::class)
            ->getSize())
        ;
        $this->assertSame(0, $dm
            ->get(RequestDatum::CLASS_PROPERTY, RequestDatum::class)
            ->getSize())
        ;
    }
}