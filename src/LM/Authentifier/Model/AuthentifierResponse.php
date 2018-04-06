<?php

namespace LM\Authentifier\Model;

use Psr\Http\Message\ResponseInterface;

/**
 * @todo Interface? After all, it is mentionned in IChallenge.
 */
class AuthentifierResponse
{
    private $process;

    private $response;

    public function __construct(
        AuthenticationProcess $process,
        ?ResponseInterface $response)
    {
        $this->process = $process;
        $this->response = $response;
    }

    public function getProcess(): AuthenticationProcess
    {
        return $this->process;
    }

    public function getHttpResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
