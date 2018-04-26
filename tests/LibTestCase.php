<?php

declare(strict_types=1);

namespace Tests\LM;

use Exception;
use LM\AuthAbstractor\Controller\AuthenticationKernel;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Model\IMember;
use LM\AuthAbstractor\Mocker\U2fMocker;
use LM\AuthAbstractor\Implementation\Member;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
use InvalidArgumentException;

/**
 * @todo Find a better name?
 * @todo Delete
 */
abstract class LibTestCase extends TestCase
{
    private $kernel;

    public function setUp()
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
                    return realpath(__DIR__.'/../vendor');
                }

                public function getCustomTwigDir(): ?string {
                    return null;
                }

                public function getLibDir(): string {
                    return realpath(__DIR__.'/..');
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

    public function getKernel(): AuthenticationKernel
    {
        return $this->kernel;
    }

    /**
     * Returns a service.
     *
     * @param string The service ID (i.e. the FQCN of the service).
     * @return mixed A service.
     */
    public function get(string $serviceId)
    {
        return $this
            ->kernel
            ->getContainer()
        ;
    }
}
