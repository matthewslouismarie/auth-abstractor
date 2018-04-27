<?php

declare(strict_types=1);

namespace Tests\LM\ChallengeTest;

use LM\AuthAbstractor\Test\KernelMocker;
use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Challenge\EmailRegistrationChallenge;
use Symfony\Component\HttpFoundation\Request;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Enum\Scalar;

class EmailRegistrationChallengeTest extends TestCase
{
    public function test()
    {
        $kernel = (new KernelMocker())->createKernel();

        $challengeResponse0 = $kernel
            ->getContainer()
            ->get(EmailRegistrationChallenge::class)
            ->process(
                new AuthenticationProcess(new TypedMap()),
                null
            )
        ;
        $this->assertFalse($challengeResponse0->isFailedAttempt());
        $this->assertFalse($challengeResponse0->isFinished());
        $this->assertNotNull($challengeResponse0->getHttpResponse());
        $httpRequest1 = (new DiactorosFactory())->createRequest(Request::create(
            'http://localhost',
            'POST',
            [
                'form' => [
                    'email' => 'user@localhost',
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
            ->get(EmailRegistrationChallenge::class)
            ->process(
                $challengeResponse0->getAuthenticationProcess(),
                $httpRequest1
            )
        ;
        $this->assertFalse($challengeResponse1->isFailedAttempt());
        $this->assertTrue($challengeResponse1->isFinished());
        $this->assertNull($challengeResponse1->getHttpResponse());
        $this->assertSame(
            'user@localhost',
            $challengeResponse1->getAuthenticationProcess()
            ->getTypedMap()
            ->get('email', Scalar::_STR)
        );
    }
}
