<?php

declare(strict_types=1);

namespace Tests\LM;

use LM\AuthAbstractor\Test\KernelMocker;
use LM\AuthAbstractor\Implementation\ApplicationConfiguration;
use LM\AuthAbstractor\Implementation\Member;
use LM\AuthAbstractor\Implementation\TestingTokenStorage;
use LM\AuthAbstractor\Mocker\U2fMocker;
use LM\AuthAbstractor\U2f\U2fRegistrationManager;
use PHPUnit\Framework\TestCase;
use Firehed\U2F\SecurityException;

class ApplicationConfigurationTest extends TestCase
{
    /**
     * @todo Test with invalid array.
     */
    public function testApplicationConfiguration()
    {
        $this->markTestSkipped('Must be updated.');

        $pwdConfig = [
            'min_length' => 5,
            'enforce_min_length' => true,
            'uppercase' => false,
            'special_chars' => false,
            'numbers' => false,
        ];
        $configuration = new ApplicationConfiguration(
            'https://example.org',
            'https://example.org/assets',
            null,
            null,
            function (string $username): bool {
                return 'user0' === $username ? true : false;
            },
            realpath(__DIR__.'/../'),
            function (string $username): Member {
                return new Member(password_hash('pwd', PASSWORD_DEFAULT), $username);
            },
            $pwdConfig,
            new TestingTokenStorage(realpath(__DIR__.'/..')),
            function (string $username): array {
                return [];
            }
        );
        $this->assertSame(
            'https://example.org',
            $configuration->getAppId()
        );
        $this->assertSame(
            'https://example.org/jquery.min.js',
            $configuration->getAssetUri('jquery.min.js')
        );
        $this->assertSame(
            '/var/www/html/example/vendor',
            $configuration->getComposerDir()
        );
        $this->assertNull($configuration->getCustomTwigDir());
        $this->assertSame(
            'user0',
            $configuration->getMember('user0')->getUsername()
        );
        $this->assertSame(
            $pwdConfig,
            $configuration->getPwdSettings()
        );
        $configuration
            ->getTokenStorage()
            ->setToken('csrf', 'blabla')
        ;
        $this->assertSame(
            'blabla',
            $configuration->getTokenStorage()->getToken('csrf')
        );
        $this->assertSame(
            $configuration->isExistingMember('user0'),
            true
        );
        $this->assertSame(
            $configuration->isExistingMember('user1'),
            false
        );
    }

    public function testNoCas()
    {
        $kernel = (new KernelMocker([]))->getKernel();
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
        $kernel = (new KernelMocker(null))->getKernel();
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
        $kernel = (new KernelMocker(glob(__DIR__.'/certificates/*.pem')))
            ->getKernel()
        ;
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
