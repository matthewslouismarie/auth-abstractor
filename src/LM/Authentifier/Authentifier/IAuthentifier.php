<?php

namespace LM\Authentifier\Authentifier;

use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\AuthentifierResponse;
use Psr\Http\Message\RequestInterface;

interface IAuthentifier
{
    public function process(
        AuthenticationProcess $authRequest,
        RequestInterface $request): AuthentifierResponse
    ;
}
