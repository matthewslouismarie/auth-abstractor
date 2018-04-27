<?php

declare(strict_types=1);

namespace Tests\LM\ChallengeTest;

use LM\AuthAbstractor\Test\KernelMocker;
use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Challenge\PasswordChallenge;
use Symfony\Component\HttpFoundation\Request;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Model\StringObject;

class PasswordChallengeTest extends TestCase
{
    public function testValidPasswordSubmission()
    {
        $kernel = (new KernelMocker())->createKernel();

        $challenge = $kernel
            ->getContainer()
            ->get(PasswordChallenge::class)
        ;

        $challengeResponse0 = $challenge->process(
            new AuthenticationProcess(
                new TypedMap([
                    'username' => new StringObject(KernelMocker::USER_ID),
                ])
            ),
            null
        );
        $this->assertFalse($challengeResponse0->isFailedAttempt());
        $this->assertFalse($challengeResponse0->isFinished());
        $this->assertNotNull($challengeResponse0->getHttpResponse());
        $httpRequest1 = (new DiactorosFactory())->createRequest(Request::create(
            'http://localhost',
            'POST',
            [
                'form' => [
                    'password' => KernelMocker::USER_PWD,
                    '_token' => $kernel
                        ->getContainer()
                        ->get(IApplicationConfiguration::class)
                        ->getTokenStorage()
                        ->getToken('form'),
                ],
            ]
        ));
        $challengeResponse1 = $challenge->process(
            $challengeResponse0->getAuthenticationProcess(),
            $httpRequest1
        );
        $this->assertFalse($challengeResponse1->isFailedAttempt());
        $this->assertTrue($challengeResponse1->isFinished());
        $this->assertNull($challengeResponse1->getHttpResponse());
    }
}
