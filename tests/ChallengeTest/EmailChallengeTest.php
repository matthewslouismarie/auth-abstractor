<?php

declare(strict_types=1);

namespace Tests\LM\ChallengeTest;

use LM\AuthAbstractor\Implementation\TestMailer;
use LM\AuthAbstractor\Test\KernelMocker;
use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Challenge\EmailChallenge;
use Symfony\Component\HttpFoundation\Request;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\Common\DataStructure\TypedMap;

class EmailChallengeTest extends TestCase
{
    public function testValidAuthentication()
    {
        $mailer = new TestMailer();
        $kernel = (new KernelMocker())->createKernel();

        $challengeResponse0 = $kernel
            ->getContainer()
            ->get(EmailChallenge::class)
            ->process(
                new AuthenticationProcess(new TypedMap([
                    'email' => 'user@localhost',
                    'mailer' => $mailer,
                ])),
                null
            )
        ;
        $httpRequest1 = (new DiactorosFactory())->createRequest(Request::create(
            'http://localhost',
            'POST',
            [
                'form' => [
                    'emailCode' => $mailer->getLastEmailBody(),
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
            ->get(EmailChallenge::class)
            ->process(
                $challengeResponse0->getAuthenticationProcess(),
                $httpRequest1
            )
        ;
        $this->assertFalse($challengeResponse1->isFailedAttempt());
        $this->assertTrue($challengeResponse1->isFinished());
    }

    public function testInvalidCode()
    {
        $mailer = new TestMailer();
        $kernel = (new KernelMocker())->createKernel();

        $challengeResponse0 = $kernel
            ->getContainer()
            ->get(EmailChallenge::class)
            ->process(
                new AuthenticationProcess(new TypedMap([
                    'email' => 'user@localhost',
                    'mailer' => $mailer,
                ])),
                null
            )
        ;
        $httpRequest1 = (new DiactorosFactory())->createRequest(Request::create(
            'http://localhost',
            'POST',
            [
                'form' => [
                    'emailCode' => -1,
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
            ->get(EmailChallenge::class)
            ->process(
                $challengeResponse0->getAuthenticationProcess(),
                $httpRequest1
            )
        ;
        $this->assertTrue($challengeResponse1->isFailedAttempt());
        $this->assertFalse($challengeResponse1->isFinished());
        $this->assertNotNull($challengeResponse1->getHttpResponse());
    }
}
