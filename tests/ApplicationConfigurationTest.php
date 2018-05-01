<?php

declare(strict_types=1);

namespace Tests\LM;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Implementation\ApplicationConfiguration;
use LM\AuthAbstractor\Implementation\Member;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;

class ApplicationConfigurationTest extends TestCase
{
    public function test()
    {
        $pwdSettings = [
            'min_length' => 5,
            'enforce_min_length' =>true,
            'uppercase' => false,
            'special_chars' => false,
            'numbers' => false,
        ];
        $member = new Member('hash', 'validusername');
        $config = new ApplicationConfiguration(
            'appid',
            'assetbaseuri',
            function ($username) use ($member) {
                if ($member->getUsername() === $username) {
                    return $member;
                }
                return null;
            },
            null
        );
        $this->assertSame('appid', $config->getAppId());
        $this->assertSame(
            'assetbaseuri/style.css',
            $config->getAssetUri('style.css')
        );
        $this->assertSame(null, $config->getCustomTwigDir());
        $this->assertSame(
            realpath(__DIR__.'/../vendor'),
            $config->getComposerDir()
        );
        $this->assertSame(
            realpath(__DIR__.'/..'),
            $config->getLibDir()
        );
        $this->assertSame(
            $member,
            $config->getMember($member->getUsername())
        );
        $this->assertTrue($config->isExistingMember($member->getUsername()));
        $this->assertFalse($config->isExistingMember('a'.$member->getUsername()));
        $this->expectException(InvalidArgumentException::class);
        $config->getMember('a'.$member->getUsername());
        $this->assertNull($config->getU2fCertificates());
        $this-assertTrue($config->getTokenStorage() instanceof NativeSessionTokenStorage);
        $this->assertSame($pwdSettings, $config->getPwdSettings());
    }

    public function testCustomTwigDir()
    {
        $config = new ApplicationConfiguration(
            'appid',
            'assetbaseuri',
            function ($username) {
                return null;
            },
            'customtwigdir'
        );
        $this->assertSame('customtwigdir', $config->getCustomTwigDir());
    }
}
