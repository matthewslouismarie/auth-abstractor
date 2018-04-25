<?php

declare(strict_types=1);

namespace LM\Authentifier\Implementation;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Model\IMember;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
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

    public function __construct(
        string $appId,
        string $assetBaseUri,
        Closure $memberFinder,
        ?string $customTwigDir = null
    ) {
        $this->appId = $appId;
        $this->assetBaseUri = $assetBaseUri;
        $this->composerDir = realpath(__DIR__.'/../../../../vendor');
        $this->memberFinder = $memberFinder;
        $this->tokenStorage = new TokenStorage();
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

    public function isExistingMember(string $username): bool
    {
        $isExistingMemberCallback = $this->isExistingMemberCallback;

        return null === ($this->memberFinder)($username);
    }
}
