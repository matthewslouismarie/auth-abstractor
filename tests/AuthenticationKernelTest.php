<?php

declare(strict_types=1);

namespace Tests\LM;

use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Test\KernelMocker;
use LM\AuthAbstractor\Factory\AuthenticationProcessFactory;
use LM\AuthAbstractor\Challenge\CredentialRegistrationChallenge;
use LM\AuthAbstractor\Implementation\EmptyCallback;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

class AuthenticationKernelTest extends TestCase
{
    public function testRegistration()
    {
        $kernel = (new KernelMocker())->createKernel();
        $process = $kernel
            ->getContainer()
            ->get(AuthenticationProcessFactory::class)
            ->createProcess([
                CredentialRegistrationChallenge::class,
            ])
        ;
        $authResponse0 = $kernel->processHttpRequest(
            (new DiactorosFactory())
                ->createRequest(Request::create('https://localhost')),
            $process,
            new EmptyCallback()
        );
        $invalidRequest = Request::create(
            'http://localhost',
            'POST',
            [
                'form' => [
                    'username' => KernelMocker::USER_ID,
                    'password' => [
                        'first' => KernelMocker::USER_ID,
                        'second' => KernelMocker::USER_PWD,
                    ],
                    'submit' => '',
                    '_token' => $kernel
                        ->getContainer()
                        ->get(IApplicationConfiguration::class)
                        ->getTokenStorage()
                        ->getToken('form'),
                ],
            ]
        );
        for ($i = 0; $i < 10; $i++) {
            $authResponse = $kernel->processHttpRequest(
                (new DiactorosFactory())->createRequest($invalidRequest),
                $process,
                new EmptyCallback()
            );
            $process = $authResponse->getProcess();
            $this->assertTrue($process->isOngoing());
        }
    }
}
