<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Implementation;

use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use LM\AuthAbstractor\Model\IMember;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
use Closure;

/**
 * This is a convenience implementation of IApplicationConfiguration.
 *
 * It removes
 * the burden of having to implement (and then instantiate)
 * IApplicationConfiguration. It assumes the library is used as a Composer
 * dependency. It also uses native PHP sessions. Finally, it does not let you
 * define the password complexity settings. If you want more control over these
 * options, you will need to provide your own implementation of
 * IApplicationConfiguration.
 *
 * @see \LM\AuthAbstractor\Configuration\IApplicationConfiguration
 * @todo Put implementations in a different library?
 */
class ApplicationConfiguration implements IApplicationConfiguration
{
    /** @var IApplicationConfiguration */
    private $appId;

    /** @var string */
    private $assetBaseUri;

    /** @var string */
    private $composerDir;

    /** @var string */
    private $libDir;

    /** @var Closure */
    private $memberFinder;

    /** @var mixed[] */
    private $pwdSettings;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var Closure */
    private $u2fRegistrationFinder;

    /**
     * @api
     * @param string $appId The application ID of the application. This is used
     * for U2F challenges. The application ID is either:
     *  - The ID of the mobile application, if the application is a mobile
     * application. The format of it differs on Android and on Apple devices.
     *  - The origin of the website (HTTPS protocol + domain name + port).
     *  - A link to an HTTPS link listing all the application IDs of the
     * application.
     * @param string $assetBaseUri The local path in which assets are stored
     * (i.e JavaScript files and CSS files).
     * @param Closure $memberFinder A callable to find a user from their
     * username. It must return null if the user does not exist.
     * @param null|string $customTwigDir A custom Twig directory for overriding
     * all, or some templates that come with auth-abstractor by default.
     */
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
