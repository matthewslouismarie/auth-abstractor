<?php

namespace LM\Authentifier\Model;

use Psr\Http\Message\ResponseInterface;

/**
 * @todo Interface? After all, it is mentionned in IAuthentifier.
 */
class AuthentifierResponse
{
    private $authenticationRequest;

    private $response;

    public function __construct(
        AuthenticationRequest $authenticationRequest,
        ResponseInterface $response)
    {
        $this->authenticationRequest = $authenticationRequest;
        $this->response = $response;
    }

    public function getAuthenticationRequest(): AuthenticationRequest
    {
        return $this->authenticationRequest;
    }

    public function getHttpResponse(): ResponseInterface
    {
        return $this->response;
    }
}
