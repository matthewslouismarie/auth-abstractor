<?php

declare(strict_types=1);

namespace Tests\LM;

use LM\AuthAbstractor\Test\KernelMocker;
use LM\AuthAbstractor\Mocker\U2fMocker;
use LM\AuthAbstractor\U2f\U2fRegistrationManager;
use PHPUnit\Framework\TestCase;
use Firehed\U2F\SecurityException;

class KernelMockerTest extends TestCase
{
    public function testNoCas()
    {
        $kernel = (new KernelMocker())->createKernel([
            KernelMocker::KEY_U2F_CERTIFICATES => [],
        ]);
        $u2fRegisterData =  $kernel
            ->getContainer()
            ->get(U2fMocker::class)
            ->get(2)
        ;
        $this->expectException(SecurityException::class);
        $kernel
            ->getContainer()
            ->get(U2fRegistrationManager::class)
            ->getU2fRegistrationFromResponse(
                $u2fRegisterData['registerResponseStr'],
                $u2fRegisterData['registerRequest']
            )
        ;
    }

    public function testDisabledCaVerification()
    {
        $kernel = (new KernelMocker())->createKernel([
            KernelMocker::KEY_U2F_CERTIFICATES => null,
        ]);
        $u2fRegisterData =  $kernel
            ->getContainer()
            ->get(U2fMocker::class)
            ->get(2)
        ;
        try {
            $kernel
                ->getContainer()
                ->get(U2fRegistrationManager::class)
                ->getU2fRegistrationFromResponse(
                    $u2fRegisterData['registerResponseStr'],
                    $u2fRegisterData['registerRequest']
                )
            ;
            $this->assertTrue(true);
        } catch (SecurityException $e) {
            $this->fail();
        }
    }

    public function testAllCas()
    {
        $kernel = (new KernelMocker())->createKernel([
            KernelMocker::KEY_U2F_CERTIFICATES => glob(__DIR__.'/certificates/*.pem'),
        ]);
        $u2fRegisterData =  $kernel
            ->getContainer()
            ->get(U2fMocker::class)
            ->get(2)
        ;
        try {
            $kernel
                ->getContainer()
                ->get(U2fRegistrationManager::class)
                ->getU2fRegistrationFromResponse(
                    $u2fRegisterData['registerResponseStr'],
                    $u2fRegisterData['registerRequest']
                )
            ;
            $this->assertTrue(true);
        } catch (SecurityException $e) {
            $this->fail();
        }
    }
}
