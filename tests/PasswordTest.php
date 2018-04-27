<?php

declare(strict_types=1);

namespace Tests\LM;

use LM\AuthAbstractor\Test\KernelMocker;
use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Challenge\CredentialRegistrationChallenge;
use LM\AuthAbstractor\Factory\AuthenticationProcessFactory;
use Symfony\Component\HttpFoundation\Request;
use LM\AuthAbstractor\Implementation\EmptyCallback;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

class PasswordTest extends TestCase
{
    public function testPasswordComplexity()
    {
        $kernel = (new KernelMocker(
            null,
            [
                'min_length' => 5,
                'enforce_min_length' =>true,
                'uppercase' => false,
                'special_chars' => true,
                'numbers' => true,
            ]
        ))->getKernel();
        $process = $kernel
            ->getContainer()
            ->get(AuthenticationProcessFactory::class)
            ->createProcess([
                CredentialRegistrationChallenge::class,
            ])
        ;
        $response = $kernel->processHttpRequest(
            (new DiactorosFactory())
                ->createRequest(Request::create('https://localhost')),
            $process,
            new EmptyCallback()
        );
        $request2 = Request::create(
            'http://localhost',
            'POST',
            [
                'form' => [
                    'username' => 'louis',
                    'password' => [
                        'first' => 'an invalid password',
                        'second' => 'an invalid password',
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
        $response = $kernel->processHttpRequest(
            (new DiactorosFactory())
                ->createRequest($request2),
            $response->getProcess(),
            new EmptyCallback()
        );
        $responseBody = $response
            ->getHttpResponse()
            ->getBody()
            ->__toString()
        ;
        $this->assertContains(
            'number',
            $responseBody
        );
    }
}
