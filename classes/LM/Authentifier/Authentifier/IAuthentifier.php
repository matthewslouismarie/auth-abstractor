<?php

namespace LM\Authentifier\Authentifier;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

interface IAuthentifier
{
    public function process(RequestInterface $request): ResponseInterface;
}
