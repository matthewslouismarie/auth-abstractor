<?php

namespace LM\Authentifier\Configuration;

use Psr\Container\ContainerInterface;
use LM\Authentifier\Model\IMember;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

interface IApplicationConfiguration
{
    public function getAssetUri(string $assetId): string;

    public function getAppId(): string;

    public function getComposerDir(): string;

    public function getContainer(): ContainerInterface;

    public function getCustomTwigDir(): ?string;

    public function getMember(string $username): IMember;

    public function getTokenStorage(): TokenStorageInterface;

    public function getU2fRegistrations(string $username): array;

    public function isExistingMember(string $username): bool;

    /**
     * @todo Delete. (Is made redundant by IAuthenticationCallback.)
     */
    public function save(): void;
}
