<?php

namespace LM\Authentifier\Model;

use LM\Authentifier\Model\AuthenticationProcess;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use Serializable;

/**
 * @todo Should actually be getSuccessResponse() and getFailureResponse()
 */
interface IAuthenticationCallback extends Serializable
{
    public function filterSuccessResponse(AuthenticationProcess $authProcess, ResponseInterface $response): ResponseInterface;

    public function filterFailureResponse(AuthenticationProcess $authProcess, ResponseInterface $response): ResponseInterface;
}
