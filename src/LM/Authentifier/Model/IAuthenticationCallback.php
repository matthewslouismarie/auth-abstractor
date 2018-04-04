<?php

namespace LM\Authentifier\Model;

use Psr\Http\Message\ResponseInterface;

interface IAuthenticationCallback
{
    public function filterSuccessResponse(ResponseInterface $response): ResponseInterface;

    public function filterFailureResponse(ResponseInterface $response): ResponseInterface;
}
