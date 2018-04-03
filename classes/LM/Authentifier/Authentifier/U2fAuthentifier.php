<?php

namespace LM\Authentifier\Authentifier;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;

class U2fAuthentifier implements IAuthentifier
{
    public function process(RequestInterface $request): ResponseInterface
    {
        $loader = new Twig_Loader_Filesystem(__DIR__."/../../../../templates");
        $twig = new Twig_Environment($loader, array(
            "cache" => __DIR__."../../../../.matthewslouismarie/authentifier/twig_cache",
        ));

        return new Response(200, [], $twig->render("u2f.html.twig"));
    }
}
