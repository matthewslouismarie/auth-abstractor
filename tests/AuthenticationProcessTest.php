<?php

namespace LM\Authentifier\Tests;

use PHPUnit\Framework\TestCase;
use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Enum\Scalar;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\StringObject;

class AuthenticationProcessTest extends TestCase
{
    public function testSerialization()
    {
        $authentifiers = [
            ExistingUsernameChallenge::class,
            U2fChallenge::class,
        ];
        $typedMap = new TypedMap([
            'used_u2f_key_public_keys' => new ArrayObject([], StringObject::class),
            'challenges' => new ArrayObject($authentifiers, Scalar::_STR),
            'status' => new Status(Status::ONGOING),
        ]);
        $authenticationProcess = new AuthenticationProcess($typedMap);
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
    }
}
