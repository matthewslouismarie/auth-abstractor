<?php

namespace LM\Authentifier\Model;

use Psr\Http\Message\ResponseInterface;

/**
 * @todo Interface? After all, it is mentionned in IAuthentifier.
 */
class AuthentifierResponse
{
    private $authenticationProcess;

    private $response;

    public function __construct(
        AuthenticationProcess $authenticationProcess,
        ResponseInterface $response)
    {
        $this->AuthenticationProcess = $authenticationProcess;
        $this->response = $response;
    }

    public function getAuthenticationProcess(): AuthenticationProcess
    {
        return $this->AuthenticationProcess;
    }

    public function getHttpResponse(): ResponseInterface
    {
        return $this->response;
    }
}
