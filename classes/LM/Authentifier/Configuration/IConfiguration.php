<?php

namespace LM\Authentifier\Configuration;

use Twig_Function;

interface IConfiguration
{
    public function getAssetUri(string $assetId): string;
}
