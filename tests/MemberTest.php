<?php

declare(strict_types=1);

namespace Tests\LM;

use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Implementation\Member;

class MemberTest extends TestCase
{
    public function testMember()
    {
        $userId = 'arandomuser';
        $userPwdHash = password_hash('pwd', PASSWORD_DEFAULT);
        $member = new Member($userPwdHash, $userId);
        $this->assertSame(
            $userId,
            $member->getUsername()
        );
        $this->assertSame(
            $userPwdHash,
            $member->getHashedPassword()
        );
        $this->assertEquals(
            $member,
            unserialize(serialize($member))
        );
    }
}
