<?php

namespace LM\Authentifier\Authentifier;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

class U2fAuthentifier implements IAuthentifier
{
    public function process(RequestInterface $request): ResponseInterface
    {
        $loader = new Twig_Loader_Filesystem("/templates");
        $twig = new Twig_Environment($loader, array(
            "cache" => "/cache",
        ));
        return new Response(200, [], $twig->render("u2f.html.twig"));
    }
}
