<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Implementation;

use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use LM\AuthAbstractor\Model\IMember;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
use Closure;

/**
 * @todo Put implementations in a different library?
 */
class ApplicationConfiguration implements IApplicationConfiguration
{
    private $assetBaseUri;

    private $composerDir;

    private $libDir;

    private $memberFinder;

    private $pwdSettings;

    private $tokenStorage;

    private $u2fRegistrationFinder;

    public function __construct(
        string $appId,
        string $assetBaseUri,
        Closure $memberFinder,
        ?string $customTwigDir = null
    ) {
        $this->appId = $appId;
        $this->assetBaseUri = $assetBaseUri;
        $this->composerDir = realpath(__DIR__.'/../../../../../..');
        $this->memberFinder = $memberFinder;
        $this->tokenStorage = new NativeSessionTokenStorage();
        $this->u2fRegistrationFinder = $u2fRegistrationFinder ?? function ($username) {
            return [];
        };
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

    public function getCustomTwigDir(): ?string
    {
        return null;
    }

    public function getLibdir(): string
    {
        return $this->composerDir.'/matthewslouismarie/auth-abstractor';
    }

    public function getMember(string $username): IMember
    {
        return ($this->memberFinder)($username);
    }

    public function getPwdSettings(): array
    {
        return [
            'min_length' => 5,
            'enforce_min_length' =>true,
            'uppercase' => false,
            'special_chars' => false,
            'numbers' => false,
        ];
    }

    public function getTokenStorage(): TokenStorageInterface
    {
        return $this->tokenStorage;
    }

    public function getU2fRegistrations(string $username): array
    {
        return ($this->u2fRegistrationFinder)($username);
    }

    public function isExistingMember(string $username): bool
    {
        return null !== ($this->memberFinder)($username);
    }
}
