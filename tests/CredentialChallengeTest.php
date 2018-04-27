<?php

declare(strict_types=1);

use LM\AuthAbstractor\Test\KernelMocker;
use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Challenge\U2fRegistrationChallenge;
use Symfony\Component\HttpFoundation\Request;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use LM\AuthAbstractor\Model\PersistOperation;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\ArrayObject;
use LM\Common\DataStructure\TypedMap;
use LM\AuthAbstractor\Challenge\CredentialChallenge;

class CredentialChallengeTest extends TestCase
{ 
    public function testCredentialChallenge()
    {
        $kernel = (new KernelMocker())->createKernel();

        $challenge = $kernel
            ->getContainer()
            ->get(CredentialChallenge::class)
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
                    'username' => KernelMocker::USER_ID,
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
    }
}
