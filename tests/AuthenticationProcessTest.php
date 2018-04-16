<?php

namespace LM\Authentifier\Tests;

use PHPUnit\Framework\TestCase;
use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\RequestDatum;
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
        $dataManager = new DataManager([
            new RequestDatum("used_u2f_key_public_keys", new ArrayObject([], StringObject::class)),
            new RequestDatum("challenges", new ArrayObject($authentifiers, Scalar::_STR)),
            new RequestDatum("status", new Status(Status::ONGOING)),
        ]);
        $authenticationProcess = new AuthenticationProcess($dataManager);
        $challenges = $authenticationProcess->getChallenges();
        $challenges->setToNextItem();
        $serializedProcess = serialize(new AuthenticationProcess($authenticationProcess
            ->getDataManager()
            ->replace(
                new RequestDatum("challenges", $challenges),
                RequestDatum::KEY_PROPERTY)))
        ;
        $unserializedProcess = unserialize($serializedProcess);
        $this->assertSame(
            U2fChallenge::class,
            $unserializedProcess->getCurrentChallenge());
    }
}