<?php

namespace LM\Authentifier\Tests;

use PHPUnit\Framework\TestCase;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\StringObject;
use LM\Authentifier\Model\RequestDatum;

class DataManagerTest extends TestCase
{
    public function testDataManager()
    {
        $array = [
            'item0' => new StringObject("Hi"),
            'item1' => new StringObject("Yo"),
            'item2' => new StringObject("Hey"),
        ];
        $dm = new TypedMap($array);
        $this->assertSame(3, $dm->getSize());
        $item = new RequestDatum("item3", );
        $retrievedItem = $dm
            ->add('item3', new IntegerObject(5), IntegerObject::class)
            ->get('item3', IntegerObject::class)
            ->getOnlyValue()
        ;
        $this->assertEquals($item, $retrievedItem);
    }
}