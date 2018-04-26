<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use Psr\Http\Message\ResponseInterface;

/**
 * This class is only used to store the value returned when the kernel finished
 * processing the HTTP request.
 *
 * @todo Add an interface.
 */
class AuthentifierResponse
{
    private $process;

    private $response;

    public function __construct(
        AuthenticationProcess $process,
        ?ResponseInterface $response
    ) {
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
