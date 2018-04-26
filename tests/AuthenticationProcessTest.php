<?php

declare(strict_types=1);

namespace Tests\LM;

use LM\AuthAbstractor\Enum\AuthenticationProcess\Status;
use LM\AuthAbstractor\Mocker\U2fMocker;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\AuthAbstractor\Factory\AuthenticationProcessFactory;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Enum\Scalar;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\StringObject;

class AuthenticationProcessTest extends LibTestCase
{
    public function testSerialization()
    {
        $this->markTestSkipped('Must be updated.');

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

    public function testU2fRegistrations()
    {
        $this->markTestSkipped('Must be updated.');

        $this->assertNotNull($this->getKernel());
        $mocker = $this->get(U2fMocker::class);
        $process = $this
            ->get(AuthenticationProcessFactory::class)
            ->createProcess(
                [
                    U2fRegistrationChallenge::class,
                ],
                3
            )
        ;
        for ($i = 0; $i < 2; $i++) {
            $u2fRegistrations = $process
                ->getTypedMap()
                ->get('u2f_registrations', Scalar::_ARRAY)
            ;
            $u2fRegistrations[] = $mocker->get($i);
            $serialized = serialize(new AuthenticationProcess(
                $process
                ->getTypedMap()
                ->set(
                    'u2f_registrations',
                    $u2fRegistrations,
                    Scalar::_ARRAY
                )
            ));
            $process = unserialize($serialized);
            $process
                ->getTypedMap()
                ->get('u2f_registrations', Scalar::_ARRAY)
            ;
        }
    }
}
