<?php

declare(strict_types=1);

namespace LM\Authentifier\Implementation;

use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Model\IMember;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Closure;

/**
 * @todo Put implementations in a different library?
 */
class ApplicationConfiguration implements IApplicationConfiguration
{
    private $assetBaseUri;

    private $composerDir;

    private $container;

    private $customTwigDir;

    private $isExistingMemberCallback;

    private $libDir;

    private $memberFinder;

    private $pwdSettings;

    private $tokenStorage;

    /**
     * @todo Find a way to define structure of the array (keys and associated
     * types) only once in this class.
     */
    public function __construct(
        string $appId,
        string $assetBaseUri,
        string $composerDir,
        ?ContainerInterface $container,
        ?string $customTwigDir,
        Closure $isExistingMemberCallback,
        string $libDir,
        Closure $memberFinder,
        array $pwdSettings,
        TokenStorageInterface $tokenStorage,
        Closure $u2fRegistrationsFinder
    ) {
        $this->appId = $appId;
        $this->assetBaseUri = $assetBaseUri;
        $this->composerDir = $composerDir;
        $this->container = $container;
        $this->customTwigDir = $customTwigDir;
        $this->isExistingMemberCallback = $isExistingMemberCallback;
        $this->libDir = $libDir;
        $this->memberFinder = $memberFinder;
        $this->pwdSettings = $pwdSettings;
        $this->tokenStorage = $tokenStorage;
        $this->u2fRegistrationsFinder = $u2fRegistrationsFinder;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function getAssetUri(string $assetId): string
    {
        return $this->assetBaseUri.'/'.$assetId;
    }

    public function getComposerDir(): string
    {
        return $this->composerDir;
    }

    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    public function getCustomTwigDir(): ?string
    {
        return $this->customTwigDir;
    }

    public function getLibdir(): string
    {
        return $this->libDir;
    }

    public function getMember(string $username): IMember
    {
        $memberFinder = $this->memberFinder;

        return $memberFinder($username);
    }

    public function getPwdSettings(): array
    {
        return $this->pwdSettings;
    }

    public function getTokenStorage(): TokenStorageInterface
    {
        return $this->tokenStorage;
    }

    public function getU2fRegistrations(string $username): array
    {
        $u2fRegistrationsFinder = $this->u2fRegistrationsFinder;

        return $u2fRegistrationsFinder($username);
    }

    public function isExistingMember(string $username): bool
    {
        $isExistingMemberCallback = $this->isExistingMemberCallback;

        return $isExistingMemberCallback($username);
    }

    /**
     * @todo Delete. (Is made redundant by IAuthenticationCallback.)
     */
    public function save(): void
    {
    }
}
