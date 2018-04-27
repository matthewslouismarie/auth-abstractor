<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use Psr\Http\Message\ResponseInterface;

/**
 * This class is only used to store the value returned when the kernel finished
 * processing the HTTP request.
 *
 * @todo Add an interface.
 * @todo Use IAuthenticationProcess interface.
 */
class AuthentifierResponse
{
    /** @var AuthenticationProcess */
    private $process;

    private $response;

    /**
     * @param AuthenticationProcess $process The current authentication process.
     * @param null|ResponseInterface $response The HTTP response.
     */
    public function __construct(
        AuthenticationProcess $process,
        ?ResponseInterface $response
    ) {
        $this->process = $process;
        $this->response = $response;
    }

    /**
     * @api
     * @return AuthenticationProcess The authentication process.
     * @todo Rename to getAuthenticationProcess()? (for consistency)
     */
    public function getProcess(): AuthenticationProcess
    {
        return $this->process;
    }

    /**
     * @api
     * @return null|ResponseInterface The HTTP response.
     */
    public function getHttpResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
