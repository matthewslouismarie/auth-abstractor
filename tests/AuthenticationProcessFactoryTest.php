<?php

declare(strict_types=1);

namespace Tests\LM;

use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Test\KernelMocker;
use LM\AuthAbstractor\Challenge\U2fChallenge;
use LM\Common\Enum\Scalar;

class AuthenticationProcessFactoryTest extends TestCase
{
    public function test()
    {
        $kernel = (new KernelMocker())->createKernel();
        $factory = $kernel->getAuthenticationProcessFactory();
        $challenges = [
            U2fChallenge::class,
        ];
        $process = $factory->createProcess($challenges);
        $this->assertSame($challenges, $process->getChallenges()->toArray(Scalar::_STR));
    }

    public function testMaxNFailedAttempts()
    {
        $kernel = (new KernelMocker())->createKernel();
        $factory = $kernel->getAuthenticationProcessFactory();
        $challenges = [
            U2fChallenge::class,
        ];
        $process = $factory->createProcess($challenges, 5);
        $this->assertSame(5, $process->getMaxNFailedAttempts());
    }

    public function testUsername()
    {
        $kernel = (new KernelMocker())->createKernel();
        $factory = $kernel->getAuthenticationProcessFactory();
        $challenges = [
            U2fChallenge::class,
        ];
        $process = $factory->createProcess($challenges, 3, 'louis');
        $this->assertSame('louis', $process->getUsername());
    }
}
