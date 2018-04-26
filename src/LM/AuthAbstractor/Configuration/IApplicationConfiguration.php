<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Configuration;

use LM\AuthAbstractor\Model\IMember;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * This is an interface that is used throughout the library to get access
 * to the environment of the application.
 *
 * By using an interface, the application
 * retains liberty on the way it wants to implement certain features, e.g. the
 * storage system used.
 *
 * @see \LM\AuthAbstractor\Implementation\ApplicationConfiguration for a
 * convenience implementation.
 */
interface IApplicationConfiguration
{
    public function getAssetUri(string $assetId): string;

    public function getAppId(): string;

    public function getComposerDir(): string;

    public function getCustomTwigDir(): ?string;

    public function getLibDir(): string;

    public function getMember(string $username): IMember;

    public function getU2fRegistrations(string $username): array;

    /**
     * @todo
     */
    public function getPwdSettings(): array;

    public function getTokenStorage(): TokenStorageInterface;

    public function isExistingMember(string $username): bool;
}
