<?php

declare(strict_types=1);

namespace Tests\LM;

use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Implementation\TestingTokenStorage;

class TestingTokenStorageTest extends TestCase
{
    public function test()
    {
        $tokenStorage = new TestingTokenStorage(realpath(__DIR__.'/..'));
        $this->assertFalse($tokenStorage->hasToken('token'));
        $tokenStorage->setToken('token', 'value');
        $this->assertTrue($tokenStorage->hasToken('token'));
        $this->assertSame('value', $tokenStorage->getToken('token'));
        $tokenStorage->removeToken('token');
        $this->assertFalse($tokenStorage->hasToken('token'));
    }
}
