<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Implementation;

use LM\AuthAbstractor\Model\IAuthenticationProcess;
use Psr\Http\Message\ResponseInterface;
use LM\AuthAbstractor\Model\IAuthenticationCallback;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

/**
 * This is a convenience implementation of IAuthenticationCallback for testing.
 * It returns an empty HTTP response.
 */
class EmptyCallback implements IAuthenticationCallback
{
    private $diactoros;

    public function __construct()
    {
        $this->diactoros = new DiactorosFactory();
    }

    public function handleFailedProcess(IAuthenticationProcess $authProcess): ResponseInterface
    {
        return $this->diactoros->createResponse(new Response());
    }

    public function handleSuccessfulProcess(IAuthenticationProcess $authProcess): ResponseInterface
    {
        return $this->diactoros->createResponse(new Response());
    }
}
