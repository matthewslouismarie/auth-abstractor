<?php

declare(strict_types=1);

namespace Tests\LM;

use LM\AuthAbstractor\Enum\AuthenticationProcess\Status;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Enum\Scalar;
use LM\Common\Model\ArrayObject;
use PHPUnit\Framework\TestCase;
use LM\Common\Model\StringObject;
use LM\Common\Model\IntegerObject;
use LM\AuthAbstractor\Implementation\Member;

class AuthenticationProcessTest extends TestCase
{
    const MAX_N_FAILED_ATTEMPTS = 5;

    public function testSerialization()
    {
        $authentifiers = [
            ExistingUsernameChallenge::class,
            U2fChallenge::class,
        ];
        $member = new Member(password_hash('yo', PASSWORD_DEFAULT), 'username');
        $usernameStr = 'username';
        $status = new Status(Status::ONGOING);
        $typedMap = new TypedMap([
            'used_u2f_key_public_keys' => new ArrayObject([], StringObject::class),
            'challenges' => new ArrayObject($authentifiers, Scalar::_STR),
            'status' => $status,
            'n_failed_attempts' => new IntegerObject(0),
            'max_n_failed_attempts' => new IntegerObject(self::MAX_N_FAILED_ATTEMPTS),
            'member' => $member,
            'username' => new StringObject($usernameStr),
        ]);
        $authenticationProcess = new AuthenticationProcess($typedMap);
        $this->assertSame($typedMap, $authenticationProcess->getTypedMap());
        $this->assertSame(self::MAX_N_FAILED_ATTEMPTS, $authenticationProcess->getMaxNFailedAttempts());
        $this->assertSame($member, $authenticationProcess->getMember());
        $this->assertSame(0, $authenticationProcess->getNFailedAttempts());
        $this->assertSame(
            1,
            $authenticationProcess
                ->incrementNFailedAttempts()
                ->getNFailedAttempts()
        );
        $this->assertSame(
            0,
            $authenticationProcess
                ->incrementNFailedAttempts()
                ->resetNFailedAttempts()
                ->getNFailedAttempts()
        );
        $this->assertSame(
            88,
            $authenticationProcess
                ->setNFailedAttempts(88)
                ->getNFailedAttempts()
        );
        $this->assertSame([], $authenticationProcess->getPersistOperations());
        $this->assertSame($status, $authenticationProcess->getStatus());
        $this->assertSame($usernameStr, $authenticationProcess->getUsername());
        $this->assertFalse($authenticationProcess->isFailed());
        $this->assertFalse($authenticationProcess->isFinished());
        $this->assertTrue($authenticationProcess->isOngoing());
        $this->assertFalse($authenticationProcess->isSucceeded());
        $challenges = $authenticationProcess->getChallenges();
        $challenges->setToNextItem();
        $serializedProcess = serialize(new AuthenticationProcess($authenticationProcess
            ->getTypedMap()
            ->set('challenges', $challenges, ArrayObject::class)))
        ;
        $unserializedProcess = unserialize($serializedProcess);
        $this->assertSame(
            U2fChallenge::class,
            $unserializedProcess->getCurrentChallenge()
        );
        $this->assertTrue(
            $authenticationProcess
                ->setToNextChallenge()
                ->setToNextChallenge()
                ->isFinished()
        );
        $this->assertEquals(
            $authenticationProcess,
            unserialize(serialize($authenticationProcess))
        );
    }
}
