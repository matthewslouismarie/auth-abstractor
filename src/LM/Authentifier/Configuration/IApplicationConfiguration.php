<?php

declare(strict_types=1);

namespace LM\Authentifier\Configuration;

use LM\Authentifier\Model\IMember;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

interface IApplicationConfiguration
{
    public function getAssetUri(string $assetId): string;

    public function getAppId(): string;

    public function getComposerDir(): string;

    public function getCustomTwigDir(): ?string;

    public function getLibDir(): string;

    public function getMember(string $username): IMember;

    /**
     * @todo
     */
    public function getPwdSettings(): array;

    public function getTokenStorage(): TokenStorageInterface;

    public function getU2fRegistrations(string $username): array;

    public function isExistingMember(string $username): bool;
}
