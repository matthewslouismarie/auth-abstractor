<?php

declare(strict_types=1);

namespace Tests\LM\ChallengeTest;

use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Implementation\Member;
use LM\AuthAbstractor\Model\PersistOperation;
use LM\AuthAbstractor\Enum\Persistence\Operation;

class PersistOperationTest extends TestCase
{
    public function test()
    {
        $member = new Member(password_hash('pwd', PASSWORD_DEFAULT), 'louis');
        $po = new PersistOperation($member, new Operation(Operation::CREATE));
        $this->assertTrue($po->getType()->is(new Operation(Operation::CREATE)));
        $this->assertSame($member, $po->getObject());
        $unserialized = unserialize(serialize($po));
        $this->assertTrue($po->getType()->is($unserialized->getType()));
        $this->assertEquals($po->getObject(), $unserialized->getObject());
    }
}
