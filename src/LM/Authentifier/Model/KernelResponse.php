<?php

namespace LM\Authentifier\Model;

use Psr\Http\Message\ResponseInterface;

/**
 * @todo Should be deleted (not used).
 */
class KernelResponse
{
    private $authProcess;

    private $httpResponse;

    public function __construct(
        AuthenticationProcess $authProcess,
        ResponseInterface $httpResponse)
    {
        $this->authProcess = $authProcess;
        $this->httpResponse = $httpResponse;
    }

    public function getProcess(): AuthenticationProcess
    {
        return $this->authProcess;
    }

    public function getHttpResponse(): ResponseInterface
    {
        return $this->httpResponse;
    }
}
