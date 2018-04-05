<?php

namespace LM\Authentifier\Configuration;

use Twig_Function;
use Psr\Container\ContainerInterface;
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

    /**
     * @todo Delete. (Is made redundant by IAuthenticationCallback.)
     */
    public function save(): void;
}
