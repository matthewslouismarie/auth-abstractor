<?php

namespace LM\Authentifier\Authentifier;

use LM\Authentifier\Model\AuthenticationRequest;
use LM\Authentifier\Model\AuthentifierResponse;
use Psr\Http\Message\RequestInterface;

interface IAuthentifier
{
    public function process(
        AuthenticationRequest $authRequest,
        RequestInterface $request): AuthentifierResponse
    ;
}
