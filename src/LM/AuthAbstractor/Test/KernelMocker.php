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
 * Mocks the kernel.
 * 
 * It only supports one user
 *
 * @internal
 * @todo Maybe it should be moved in tests?
 */
class KernelMocker
{
    const KEY_PWD_SETTINGS = 'pwdSettings';

    const KEY_U2F_CERTIFICATES = 'u2fCertificates';

    const KEY_USER_ID = 'userId';

    const KEY_USER_PWD = 'userPwd';

    const USER_ID = 'user';

    const USER_PWD = 'pwd';

    /**
     * @param array $options An array of options for initialising the kernel.
     * The available options are the constants of KernelMocker that begin with
     * KEY_.
     * @return IAuthenticationKernel A kernel that can be used for unit testing.
     */
    public function createKernel(array $options = []): IAuthenticationKernel
    {
        if (!isset($options[self::KEY_U2F_CERTIFICATES])) {
            $options[self::KEY_U2F_CERTIFICATES] = null;
        }
        if (!isset($options[self::KEY_PWD_SETTINGS])) {
            $options[self::KEY_PWD_SETTINGS] = [
                'min_length' => 5,
                'enforce_min_length' =>true,
                'uppercase' => false,
                'special_chars' => false,
                'numbers' => false,
            ];
        }
        if (!isset($options[self::KEY_USER_ID])) {
            $options[self::KEY_USER_ID] = self::USER_ID;
        }
        if (!isset($options[self::KEY_USER_PWD])) {
            $options[self::KEY_USER_PWD] = self::USER_PWD;
        }
        return new AuthenticationKernel(
            new class(
                $options[self::KEY_U2F_CERTIFICATES],
                $options[self::KEY_PWD_SETTINGS],
                $options[self::KEY_USER_ID],
                $options[self::KEY_USER_PWD]
            ) implements IApplicationConfiguration {
                private $cas;

                private $hashedPassword;

                private $tokenStorage;
            
                private $username;

                public function __construct(
                    ?array $cas,
                    array $pwdSettings,
                    string $username,
                    string $password
                ) {
                    $this->cas = $cas;
                    $this->hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $this->pwdSettings = $pwdSettings;
                    $this->tokenStorage = new SessionTokenStorage(new Session(
                        new MockArraySessionStorage()
                    ));
                    $this->username = $username;
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
                    if ($this->username !== $username) {
                        throw new InvalidArgumentException();
                    }
                    return new Member(
                        $this->hashedPassword,
                        $this->username
                    );
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
                    return $this->username === $username;
                }
            }
        );
    }
}
