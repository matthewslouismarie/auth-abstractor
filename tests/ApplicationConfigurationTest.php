<?php

declare(strict_types=1);

namespace Tests\LM;

use LM\AuthAbstractor\Implementation\ApplicationConfiguration;
use LM\AuthAbstractor\Implementation\Member;
use LM\AuthAbstractor\Implementation\TestingTokenStorage;
use PHPUnit\Framework\TestCase;

class ApplicationConfigurationTest extends TestCase
{
    /**
     * @todo Test with invalid array.
     */
    public function testApplicationConfiguration()
    {
        $pwdConfig = [
            'min_length' => 5,
            'enforce_min_length' => true,
            'uppercase' => false,
            'special_chars' => false,
            'numbers' => false,
        ];
        $configuration = new ApplicationConfiguration(
            'https://example.org',
            'https://example.org',
            '/var/www/html/example/vendor',
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
}
