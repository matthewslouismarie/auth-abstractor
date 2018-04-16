<?php

namespace LM\Authentifier\Tests;

use InvalidArgumentException;
use LM\Authentifier\Implementation\Member;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Enum\Scalar;
use PHPUnit\Framework\TestCase;

class TypedMapTest extends TestCase
{
    public function testMap()
    {
        $map = new TypedMap([
            'key0' => 'Value 0',
            'key1' => 4,
            'key2' => true,
            'key3' => new Member(password_hash('', PASSWORD_DEFAULT), 'username'),
            'key4' => new Member(password_hash('', PASSWORD_DEFAULT), 'username2'),
        ]);
        $this->assertSame(5, $map->getSize());
        $this->assertSame(
            'Value 0',
            $map->get('key0', Scalar::_STR))
        ;
        $this->expectException(InvalidArgumentException::class);
        $map->get('key0', Scalar::_INT);
    }

    public function testKeyValidation()
    {
        $this->expectException(InvalidArgumentException::class);
        new TypedMap([
            5 => 'Value 0',
        ]);
    }

    public function testSerialization()
    {
        $map = new TypedMap([
            'key0' => 'Value 0',
            'key1' => 4,
            'key2' => true,
        ]);
        $this->assertSame(
            $map->toArray(),
            unserialize(serialize($map))->toArray())
        ;
    }
}
