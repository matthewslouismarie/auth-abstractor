<?php

declare(strict_types=1);

namespace Tests\LM;

use LM\AuthAbstractor\Test\KernelMocker;
use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Challenge\U2fRegistrationChallenge;
use Symfony\Component\HttpFoundation\Request;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use LM\AuthAbstractor\Model\PersistOperation;
use LM\AuthAbstractor\Mocker\U2fMocker;
use LM\AuthAbstractor\Model\U2fRegistrationRequest;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\ArrayObject;
use LM\Common\DataStructure\TypedMap;

class U2fRegistrationChallengeTest extends TestCase
{
    public function testValidU2fRegistration()
    {
        $kernel = (new KernelMocker())->getKernel();

        $challengeResponse0 = $kernel
            ->getContainer()
            ->get(U2fRegistrationChallenge::class)
            ->process(
                new AuthenticationProcess(new TypedMap([
                    'u2f_registrations' => [],
                    'n_u2f_registrations' => new IntegerObject(0),
                    'persist_operations' => new ArrayObject([], PersistOperation::class),
                ])),
                null
            )
        ;
        $this->assertFalse($challengeResponse0->isFailedAttempt());
        $this->assertFalse($challengeResponse0->isFinished());
        $u2fData = $kernel
            ->getContainer()
            ->get(U2fMocker::class)
            ->get(2)
        ;
        $process1 = new AuthenticationProcess(
            $challengeResponse0
            ->getAuthenticationProcess()
            ->getTypedMap()
            ->set(
                'current_u2f_registration_request',
                new U2fRegistrationRequest($u2fData['registerRequest']),
                U2fRegistrationRequest::class
            )
        );
        $httpRequest1 = (new DiactorosFactory())->createRequest(Request::create(
            'http://localhost',
            'POST',
            [
                'form' => [
                    'u2fDeviceResponse' => $u2fData['registerResponseStr'],
                    '_token' => $kernel
                        ->getContainer()
                        ->get(IApplicationConfiguration::class)
                        ->getTokenStorage()
                        ->getToken('form'),
                ],
            ]
        ));
        $challengeResponse1 = $kernel
            ->getContainer()
            ->get(U2fRegistrationChallenge::class)
            ->process(
                $process1,
                $httpRequest1
            )
        ;
        $this->assertFalse($challengeResponse1->isFailedAttempt());
        $this->assertTrue($challengeResponse1->isFinished());
    }
}
