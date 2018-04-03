<?php

namespace LM\Authentifier\Authentifier;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

class U2fAuthentifier implements IAuthentifier
{
    public function process(RequestInterface $request): ResponseInterface
    {
        return new Response(200, [], "U2fAuthentifier");
    }
}
