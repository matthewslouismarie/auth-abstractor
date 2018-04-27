<?php

declare(strict_types=1);

namespace Tests\LM;

use PHPUnit\Framework\TestCase;
use LM\Common\DataStructure\TypedMap;
use LM\AuthAbstractor\Enum\AuthenticationProcess\Status;
use LM\AuthAbstractor\Challenge\EmptyChallenge;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\Common\Enum\Scalar;
use LM\Common\Model\ArrayObject;
use LM\AuthAbstractor\Test\KernelMocker;
use LM\AuthAbstractor\Controller\AuthenticationProcessHandler;
use LM\AuthAbstractor\Implementation\EmptyCallback;
use LM\Common\Model\IntegerObject;

class AuthenticationProcessHandlerTest extends TestCase
{
    public function testOngoing()
    {
        $kernel = (new KernelMocker)->createKernel();
        $process = new AuthenticationProcess(new TypedMap([
            'status' => new Status(Status::ONGOING),
            'max_n_failed_attempts' => new IntegerObject(3),
            'n_failed_attempts' => new IntegerObject(0),
            'challenges' => new ArrayObject(
                [
                    EmptyChallenge::class,
                ],
                Scalar::_STR
            ),
        ]));
        $handler = $kernel
            ->getContainer()
            ->get(AuthenticationProcessHandler::class)
        ;
        $authResponse = $handler->handleAuthenticationProcess(
            null,
            $process,
            new EmptyCallback()
        );
        $newProcess = $authResponse->getProcess();
        $this->assertNull($authResponse->getHttpResponse());
        $this->assertSame(
            $process->getMaxNFailedAttempts(),
            $newProcess->getMaxNFailedAttempts()
        );
        $this->assertSame(
            $process->getNFailedAttempts(),
            $newProcess->getNFailedAttempts()
        );
        $this->assertSame(
            $process->getNFailedAttempts(),
            $newProcess->getNFailedAttempts()
        );
        $this->assertTrue($newProcess->isSucceeded());
    }
}
