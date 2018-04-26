<?php

namespace LM\AuthAbstractor\Test;

use Exception;
use LM\AuthAbstractor\Controller\AuthenticationKernel;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Model\IMember;
use LM\AuthAbstractor\Model\IAuthenticationKernel;
use LM\AuthAbstractor\Mocker\U2fMocker;
use LM\AuthAbstractor\Implementation\Member;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
use InvalidArgumentException;

/**
 * @internal
 * @todo Maybe it should be moved in tests?
 */
class KernelMocker
{
    private $kernel;

    public function __construct()
    {
        $this->kernel = new AuthenticationKernel(
            new class() implements IApplicationConfiguration {
                const USERNAME = 'user';

                private $tokenStorage;

                public function __construct()
                {
                    $this->tokenStorage = new NativeSessionTokenStorage();
                }

                public function getAssetUri(string $assetId): string {
                    return 'https://example.org';
                }

                public function getAppId(): string {
                    return 'https://example.org';
                }

                public function getComposerDir(): string {
                    return realpath(__DIR__.'/../../../../vendor');
                }

                public function getCustomTwigDir(): ?string {
                    return null;
                }

                public function getLibDir(): string {
                    return realpath(__DIR__.'/../../../..');
                }

                public function getMember(string $username): IMember {
                    if (self::USERNAME !== $username) {
                        throw new InvalidArgumentException();
                    }
                    return new Member(password_hash('pwd', PASSWORD_DEFAULT), 'user');
                }

                public function getU2fCertificates(): ?array {
                    return null;
                }

                public function getU2fRegistrations(string $username): array {
                    return (new U2fMocker($this))->getU2fRegistrations();
                }

                public function getPwdSettings(): array {
                    return [
                        'min_length' => 5,
                        'enforce_min_length' =>true,
                        'uppercase' => false,
                        'special_chars' => false,
                        'numbers' => false,
                    ];
                }

                public function getTokenStorage(): TokenStorageInterface {
                    return $this->tokenStorage;
                }

                public function isExistingMember(string $username): bool {
                    return self::USERNAME === $member->getUsername();
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