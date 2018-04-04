<?php

namespace LM\Authentifier\Configuration;

use Twig_Function;

/**
 * @todo Rename to IExternalConfiguration or ExternalEnvironment or… ?
 * Or even better, UserConfiguration.
 */
interface IApplicationConfiguration
{
    public function getAssetUri(string $assetId): string;

    public function getAppId(): string;

    public function save(): void;
}
