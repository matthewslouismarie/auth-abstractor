<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Implementation;

use Psr\Http\Message\ResponseInterface;
use LM\AuthAbstractor\Model\IAuthenticationProcess;
use LM\AuthAbstractor\Model\IAuthentifierResponse;

/**
 * This class is only used to store the value returned when the kernel finished
 * processing the HTTP request.
 */
class AuthentifierResponse implements IAuthentifierResponse
{
    /** @var AuthenticationProcess */
    private $process;

    /** @var ResponseInterface|null */
    private $response;

    /**
     * @param IAuthenticationProcess $process The authentication process.
     * @param null|ResponseInterface $response The HTTP response, if any.
     */
    public function __construct(
        IAuthenticationProcess $process,
        ?ResponseInterface $response
    ) {
        $this->process = $process;
        $this->response = $response;
    }

    public function getAuthenticationProcess(): IAuthenticationProcess
    {
        return $this->process;
    }

    public function getHttpResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
