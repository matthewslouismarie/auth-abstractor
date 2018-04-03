<?php

namespace LM\Authentifier\Authentifier;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Twig_Environment;
use Twig_Function;
use Twig_Loader_Filesystem;
use LM\Authentifier\Configuration\IConfiguration;

class U2fAuthentifier implements IAuthentifier
{
    private $config;

    public function __construct(IConfiguration $config)
    {
        $this->config = $config;
    }

    public function process(RequestInterface $request): ResponseInterface
    {
        $loader = new Twig_Loader_Filesystem(__DIR__."/../../../../templates");
        $twig = new Twig_Environment($loader, [
            "cache" => false,
        ]);
        $assetFunction = new Twig_Function("asset", [$this->config, "getAssetUri"]);
        $twig->addFunction($assetFunction);
        return new Response(200, [], $twig->render("u2f.html.twig"));
    }
}
