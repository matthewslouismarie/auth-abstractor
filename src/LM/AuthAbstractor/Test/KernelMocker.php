<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Test;

use LM\AuthAbstractor\Controller\AuthenticationKernel;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use LM\AuthAbstractor\Model\IMember;
use LM\AuthAbstractor\Model\IAuthenticationKernel;
use LM\AuthAbstractor\Mocker\U2fMocker;
use LM\AuthAbstractor\Implementation\Member;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @internal
 * @todo Maybe it should be moved in tests?
 */
class KernelMocker
{
    private $kernel;

    public function __construct(
        ?array $cas = null,
        ?array $pwdSettings = null
    ) {
        $this->kernel = new AuthenticationKernel(
            new class($cas, $pwdSettings) implements IApplicationConfiguration {
                const USERNAME = 'user';

                private $cas;

                private $tokenStorage;

                public function __construct(
                    ?array $cas,
                    ?array $pwdSettings
                ) {
                    $this->cas = $cas;
                    $this->pwdSettings = $pwdSettings ?? [
                        'min_length' => 5,
                        'enforce_min_length' =>true,
                        'uppercase' => false,
                        'special_chars' => false,
                        'numbers' => false,
                    ];
                    $this->tokenStorage = new SessionTokenStorage(new Session(
                        new MockArraySessionStorage()
                    ));
                }

                public function getAssetUri(string $assetId): string
                {
                    return 'https://example.org';
                }

                public function getAppId(): string
                {
                    return 'https://example.org';
                }

                public function getComposerDir(): string
                {
                    return realpath(__DIR__.'/../../../../vendor');
                }

                public function getCustomTwigDir(): ?string
                {
                    return null;
                }

                public function getLibDir(): string
                {
                    return realpath(__DIR__.'/../../../..');
                }

                public function getMember(string $username): IMember
                {
                    if (self::USERNAME !== $username) {
                        throw new InvalidArgumentException();
                    }
                    return new Member(password_hash('pwd', PASSWORD_DEFAULT), 'user');
                }

                public function getU2fCertificates(): ?array
                {
                    return $this->cas;
                }

                public function getU2fRegistrations(string $username): array
                {
                    return (new U2fMocker($this))->getU2fRegistrations();
                }

                public function getPwdSettings(): array
                {
                    return $this->pwdSettings;
                }

                public function getTokenStorage(): TokenStorageInterface
                {
                    return $this->tokenStorage;
                }

                public function isExistingMember(string $username): bool
                {
                    return self::USERNAME === $username;
                }
            }
        );
    }

    /**
     * @return IAuthenticationKernel A kernel that can be used for unit testing.
     */
    public function getKernel(): IAuthenticationKernel
    {
        return $this->kernel;
    }
}
