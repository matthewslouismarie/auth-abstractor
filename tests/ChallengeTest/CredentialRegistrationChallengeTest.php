<?php

declare(strict_types=1);

namespace Tests\LM\ChallengeTest;

use LM\AuthAbstractor\Test\KernelMocker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\Common\DataStructure\TypedMap;
use LM\AuthAbstractor\Challenge\CredentialRegistrationChallenge;

class CredentialRegistrationChallengeTest extends TestCase
{
    public function testValidCredentialRegistration()
    {
        $kernel = (new KernelMocker())->createKernel();

        $challenge = $kernel
            ->getContainer()
            ->get(CredentialRegistrationChallenge::class)
        ;

        $challengeResponse0 = $challenge->process(
            new AuthenticationProcess(new TypedMap([])),
            null
        );
        $this->assertFalse($challengeResponse0->isFailedAttempt());
        $this->assertFalse($challengeResponse0->isFinished());
        $httpRequest1 = (new DiactorosFactory())->createRequest(Request::create(
            'http://localhost',
            'POST',
            [
                'form' => [
                    'username' => 'new::'.KernelMocker::USER_ID,
                    'password' => [
                        'first' => KernelMocker::USER_PWD,
                        'second' => KernelMocker::USER_PWD,
                    ],
                    '_token' => $kernel
                        ->getContainer()
                        ->get(IApplicationConfiguration::class)
                        ->getTokenStorage()
                        ->getToken('form'),
                ],
            ]
        ));
        $this->assertSame(
            0,
            count(
                $challengeResponse0
                ->getAuthenticationProcess()
                ->getPersistOperations()
            )
        );
        $challengeResponse1 = $challenge->process(
            $challengeResponse0->getAuthenticationProcess(),
            $httpRequest1
        );
        $this->assertFalse($challengeResponse1->isFailedAttempt());
        $this->assertTrue($challengeResponse1->isFinished());
        $this->assertNull($challengeResponse1->getHttpResponse());
        $this->assertSame(
            1,
            count(
                $challengeResponse1
                ->getAuthenticationProcess()
                ->getPersistOperations()
            )
        );
    }
}
