<?php

namespace LM\Authentifier\Configuration;

use Twig_Function;
use Psr\Container\ContainerInterface;
use LM\Authentifier\Model\IU2fRegistrationFetcher;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * @todo Rename to IExternalConfiguration or ExternalEnvironment or… ?
 * Or even better, UserConfiguration.
 */
interface IApplicationConfiguration
{
    public function getAssetUri(string $assetId): string;

    public function getAppId(): string;

    public function getContainer(): ContainerInterface;

    public function getTokenStorage(): TokenStorageInterface;

    public function getU2fRegistrations(string $username): array;

    public function isExistingMember(string $username): bool;

    /**
     * @todo Delete. (Is made redundant by IAuthenticationCallback.)
     */
    public function save(): void;
}
